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
    public $selectedConversation = null;
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
        if ($this->selectedConversation) {
            $messages = Mensaje::where(function($q) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $this->selectedConversation->id);
            })->orWhere(function($q) {
                $q->where('sender_id', $this->selectedConversation->id)->where('receiver_id', auth()->id());
            })->orderBy('created_at', 'asc')->get();
        }

        return view('livewire.mensajes.index', [
            'conversations' => $conversations,
            'users' => $users,
            'messages' => $messages,
        ]);
    }

    public function getConversations()
    {
        return User::where('tenant_id', auth()->user()->tenant_id)
            ->where('id', '!=', auth()->id())
            ->whereHas('sentMessages', function($q) {
                $q->where('receiver_id', auth()->id());
            })
            ->orWhereHas('receivedMessages', function($q) {
                $q->where('sender_id', auth()->id());
            })
            ->withCount(['receivedMessages as unread_count' => function($q) {
                $q->where('receiver_id', auth()->id())->where('leido', false);
            }])
            ->get()
            ->map(function($user) {
                $user->last_message = Mensaje::where(function($q) use ($user) {
                    $q->where('sender_id', auth()->id())->where('receiver_id', $user->id);
                })->orWhere(function($q) use ($user) {
                    $q->where('sender_id', $user->id)->where('receiver_id', auth()->id());
                })->latest()->first();
                return $user;
            })
            ->sortByDesc(function($user) {
                return $user->last_message?->created_at;
            });
    }

    public function selectConversation($userId)
    {
        $this->selectedConversation = User::find($userId);
        $this->markConversationAsRead($userId);
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

        $mensaje = Mensaje::create([
            'tenant_id' => auth()->user()->tenant_id,
            'sender_id' => auth()->id(),
            'receiver_id' => $this->selectedConversation->id,
            'contenido' => $this->replyContent,
            'leido' => false,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'accion' => 'send_message',
            'modulo' => 'mensajes',
            'descripcion' => 'Envió un mensaje a ' . $this->selectedConversation->name,
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

        $mensaje = Mensaje::create([
            'tenant_id' => auth()->user()->tenant_id,
            'sender_id' => auth()->id(),
            'receiver_id' => $this->receiver_id,
            'contenido' => $this->contenido,
            'leido' => false,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'accion' => 'send_message',
            'modulo' => 'mensajes',
            'descripcion' => 'Envió un mensaje a ' . User::find($this->receiver_id)->name,
            'metadatos' => ['mensaje_id' => $mensaje->id],
            'ip_address' => request()->ip(),
        ]);

        $this->showModal = false;
        $this->dispatch('notify', 'Mensaje enviado exitosamente');
        $this->dispatch('message-sent');
        $this->selectConversation($this->receiver_id);
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
