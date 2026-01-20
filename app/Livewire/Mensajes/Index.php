<?php

namespace App\Livewire\Mensajes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Mensaje;
use App\Models\User;
use App\Models\AuditLog;

use Livewire\WithFileUploads;

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
        $users = User::where('tenant_id', auth()->user()->tenant_id)
            ->where('id', '!=', auth()->id())
            ->get();

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

        return view('livewire.mensajes.index', [
            'conversations' => $conversations,
            'users' => $users,
            'messages' => $messages,
            'selectedConversation' => $selectedConversation,
        ]);
    }

    public function getConversations()
    {
        return User::where('tenant_id', auth()->user()->tenant_id)
            ->where('id', '!=', auth()->id())
            ->where(function($query) {
                $query->whereHas('sentMessages', function($q) {
                    $q->where('receiver_id', auth()->id());
                })
                ->orWhereHas('receivedMessages', function($q) {
                    $q->where('sender_id', auth()->id());
                });
            })
            ->withCount(['receivedMessages as unread_count' => function($q) {
                $q->where('receiver_id', auth()->id())->where('leido', false);
            }])
            ->get()
            ->map(function($user) {
                $user->last_message = Mensaje::where(function($q) use ($user) {
                    $q->where(function($sub) use ($user) {
                        $sub->where('sender_id', auth()->id())->where('receiver_id', $user->id);
                    })->orWhere(function($sub) use ($user) {
                        $sub->where('sender_id', $user->id)->where('receiver_id', auth()->id());
                    });
                })->latest()->first();
                return $user;
            })
            ->sortByDesc(function($user) {
                return $user->last_message?->created_at;
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

        $receiver = User::find($this->selectedConversationId);
        $tenantId = auth()->user()->tenant_id ?? ($receiver ? $receiver->tenant_id : null);

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
    }

    public function create()
    {
        $this->reset(['receiver_id', 'contenido', 'attachment']);
        $this->showModal = true;
    }

    public function send()
    {
        $this->validate();

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($this->attachment) {
            $attachmentName = $this->attachment->getClientOriginalName();
            $attachmentType = $this->attachment->getMimeType();
            $attachmentPath = $this->attachment->store('attachments', 'public');
        }

        $receiver = User::find($this->receiver_id);
        $tenantId = auth()->user()->tenant_id ?? ($receiver ? $receiver->tenant_id : null);

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
        $this->selectConversation($this->selectedConversationId);
    }

    public function markAsRead($id)
    {
        $mensaje = Mensaje::findOrFail($id);
        if ($mensaje->receiver_id === auth()->id()) {
            $mensaje->update(['leido' => true]);
            $this->dispatch('message-read');
        }
    }
}
