<?php

namespace App\Livewire\Mensajes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Mensaje;
use App\Models\User;
use App\Models\AuditLog;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $showModal = false;
    public $receiver_id;
    public $contenido;
    public $selectedConversationId = null;
    public $replyContent = '';
    public $selectedMessageId = null;
    public $attachment;
    public $showEmojiPicker = false;
    public $isTyping = false;

    protected $queryString = ['selectedMessageId' => ['as' => 'message']];

    protected $rules = [
        'receiver_id' => 'required|exists:users,id',
        'contenido' => 'required|string|max:1000',
        'attachment' => 'nullable|file|max:10240', // 10MB max
    ];

    public function mount()
    {
        if ($this->selectedMessageId) {
            $message = Mensaje::find($this->selectedMessageId);
            if ($message) {
                $otherUserId = $message->sender_id === auth()->id() ? $message->receiver_id : $message->sender_id;
                $this->selectConversation($otherUserId);
                $this->markAsRead($this->selectedMessageId);
            }
        }
    }

    public function render()
    {
        $conversations = $this->getConversations();
        $user = auth()->user();
        $usersQuery = User::where('id', '!=', $user->id);
        
        if ($user->role !== 'super_admin') {
            $usersQuery->where('tenant_id', $user->tenant_id);
        }
        
        $users = $usersQuery->get();

        $messages = [];
        $selectedConversation = null;
        if ($this->selectedConversationId) {
            $selectedConversation = User::find($this->selectedConversationId);
            $messages = Mensaje::where(function($q) {
                $q->where(function($sub) {
                    $sub->where('sender_id', auth()->id())->where('receiver_id', $this->selectedConversationId);
                })->orWhere(function($sub) {
                    $sub->where('sender_id', $this->selectedConversationId)->where('receiver_id', auth()->id());
                });
            })->orderBy('created_at', 'asc')->get();
        }

        Log::info('Rendering Mensajes Index', [
            'selectedConversationId' => $this->selectedConversationId,
            'showModal' => $this->showModal,
            'receiver_id' => $this->receiver_id
        ]);

        return view('livewire.mensajes.index', [
            'conversations' => $conversations,
            'users' => $users,
            'messages' => $messages,
            'selectedConversation' => $selectedConversation,
        ]);
    }

    public function getConversations()
    {
        $user = auth()->user();
        
        $query = User::query();

        // Si no es super_admin, filtrar por tenant
        if ($user->role !== 'super_admin') {
            $query->where('tenant_id', $user->tenant_id);
        }

        $query->where('id', '!=', $user->id)
            ->where(function($q) use ($user) {
                $q->whereHas('sentMessages', function($sq) use ($user) {
                    $sq->where('receiver_id', $user->id);
                })
                ->orWhereHas('receivedMessages', function($sq) use ($user) {
                    $sq->where('sender_id', $user->id);
                });
            });

        Log::info('Consultando conversaciones', [
            'user_id' => $user->id,
            'role' => $user->role,
            'tenant_id' => $user->tenant_id,
            'count' => $query->count()
        ]);

        return $query->withCount(['receivedMessages as unread_count' => function($q) use ($user) {
                $q->where('receiver_id', $user->id)->where('leido', false);
            }])
            ->get()
            ->map(function($contact) use ($user) {
                $contact->last_message = Mensaje::where(function($q) use ($contact, $user) {
                    $q->where(function($sub) use ($contact, $user) {
                        $sub->where('sender_id', $user->id)->where('receiver_id', $contact->id);
                    })->orWhere(function($sub) use ($contact, $user) {
                        $sub->where('sender_id', $contact->id)->where('receiver_id', $user->id);
                    });
                })->latest()->first();
                return $contact;
            })
            ->sortByDesc(function($contact) {
                return $contact->last_message?->created_at;
            });
    }

    public function selectConversation($userId)
    {
        $this->selectedConversationId = $userId;
        $this->markConversationAsRead($userId);
        $this->dispatch('message-sent'); // Trigger scroll
    }

    public function markConversationAsRead($userId)
    {
        Mensaje::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('leido', false)
            ->update(['leido' => true]);
        
        $this->dispatch('message-read');
    }

    public function sendReply()
    {
        $this->validate([
            'replyContent' => 'required|string|max:1000',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($this->attachment) {
            $attachmentName = $this->attachment->getClientOriginalName();
            $attachmentType = $this->attachment->getMimeType();
            $attachmentPath = $this->attachment->store('attachments', 'public');
        }

        try {
            $receiver = User::find($this->selectedConversationId);
            $tenantId = auth()->user()->tenant_id ?? ($receiver ? $receiver->tenant_id : null);

            Log::info('Intentando enviar respuesta', [
                'sender_id' => auth()->id(),
                'receiver_id' => $this->selectedConversationId,
                'tenant_id' => $tenantId
            ]);

            $mensaje = Mensaje::create([
                'tenant_id' => $tenantId,
                'sender_id' => auth()->id(),
                'receiver_id' => $this->selectedConversationId,
                'contenido' => $this->replyContent,
                'leido' => false,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_type' => $attachmentType,
            ]);

            AuditLog::create([
                'tenant_id' => $tenantId,
                'user_id' => auth()->id(),
                'accion' => 'send_message',
                'modulo' => 'mensajes',
                'descripcion' => 'Envió un mensaje a ' . ($receiver ? $receiver->name : 'Usuario desconocido'),
                'metadatos' => ['mensaje_id' => $mensaje->id],
                'ip_address' => request()->ip(),
            ]);

            $this->replyContent = '';
            $this->attachment = null;
            $this->dispatch('message-sent');
            $this->dispatch('new-message-received')->to('layout.messages-notification');
            
            Log::info('Respuesta enviada con éxito', ['mensaje_id' => $mensaje->id]);
        } catch (\Exception $e) {
            Log::error('Error al enviar respuesta: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('notify', 'Error al enviar el mensaje: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $this->reset(['receiver_id', 'contenido', 'attachment']);
        $this->showModal = true;
    }

    public function send()
    {
        Log::info('Metodo send() iniciado', [
            'receiver_id' => $this->receiver_id,
            'contenido' => $this->contenido,
            'sender_id' => auth()->id()
        ]);

        $this->validate();

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($this->attachment) {
            $attachmentName = $this->attachment->getClientOriginalName();
            $attachmentType = $this->attachment->getMimeType();
            $attachmentPath = $this->attachment->store('attachments', 'public');
        }

        try {
            $receiver = User::find($this->receiver_id);
            $tenantId = auth()->user()->tenant_id ?? ($receiver ? $receiver->tenant_id : null);

            Log::info('Intentando iniciar nueva conversación', [
                'sender_id' => auth()->id(),
                'receiver_id' => $this->receiver_id,
                'tenant_id' => $tenantId
            ]);

            $mensaje = Mensaje::create([
                'tenant_id' => $tenantId,
                'sender_id' => auth()->id(),
                'receiver_id' => $this->receiver_id,
                'contenido' => $this->contenido,
                'leido' => false,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_type' => $attachmentType,
            ]);

            AuditLog::create([
                'tenant_id' => $tenantId,
                'user_id' => auth()->id(),
                'accion' => 'send_message',
                'modulo' => 'mensajes',
                'descripcion' => 'Envió un mensaje a ' . ($receiver ? $receiver->name : 'Usuario desconocido'),
                'metadatos' => ['mensaje_id' => $mensaje->id],
                'ip_address' => request()->ip(),
            ]);

            $this->showModal = false;
            $this->selectedConversationId = $this->receiver_id;
            $this->reset(['receiver_id', 'contenido', 'attachment']);
            
            $this->dispatch('notify', 'Mensaje enviado exitosamente');
            $this->dispatch('message-sent');
            $this->dispatch('new-message-received')->to('layout.messages-notification');
            
            // Forzar la selección de la conversación para que aparezca en la vista
            $this->selectConversation($this->selectedConversationId);

            Log::info('Nueva conversación iniciada con éxito', ['mensaje_id' => $mensaje->id]);
        } catch (\Exception $e) {
            if (class_exists('Illuminate\Support\Facades\Log')) {
                Log::error('Error al iniciar conversación: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
            }
            $this->dispatch('notify', 'Error al iniciar conversación: ' . $e->getMessage());
        }
    }

    public function markAsRead($id)
    {
        $mensaje = Mensaje::findOrFail($id);
        if ($mensaje->receiver_id === auth()->id()) {
            $mensaje->update(['leido' => true]);
            $this->dispatch('message-read');
        }
    }

    protected function exceptionHandler($e)
    {
        Log::error('Excepción en Mensajes Index: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    protected function onValidationFailed()
    {
        Log::warning('Validación fallida en Mensajes Index', [
            'errors' => $this->getErrorBag()->toArray(),
            'data' => [
                'receiver_id' => $this->receiver_id,
                'contenido' => $this->contenido,
                'replyContent' => $this->replyContent
            ]
        ]);
    }
}
