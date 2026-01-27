<?php

namespace App\Livewire\Layout;

use Livewire\Component;
use App\Models\Mensaje;
use App\Models\Expediente;
use App\Models\Comentario;
use Livewire\Attributes\On;

class MessagesNotification extends Component
{
    public $unreadCount = 0;
    public $recentMessages = [];
    public $showDropdown = false;

    public function mount()
    {
        $this->loadUnreadCount();
        $this->loadRecentMessages();
    }

    public function loadUnreadCount()
    {
        $user = auth()->user();
        $expedienteIds = Expediente::where('abogado_responsable_id', $user->id)
            ->orWhereHas('assignedUsers', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->pluck('id');

        $readCommentIds = \DB::table('comentario_reads')
            ->where('user_id', $user->id)
            ->pluck('comentario_id');

        $this->unreadCount = Comentario::whereIn('expediente_id', $expedienteIds)
            ->where('created_at', '>=', now()->subDay())
            ->whereNotIn('id', $readCommentIds)
            ->count();
    }

    public function loadRecentMessages()
    {
        $user = auth()->user();
        $expedienteIds = Expediente::where('abogado_responsable_id', $user->id)
            ->orWhereHas('assignedUsers', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->pluck('id');

        $readCommentIds = \DB::table('comentario_reads')
            ->where('user_id', $user->id)
            ->pluck('comentario_id');

        $this->recentMessages = Comentario::whereIn('expediente_id', $expedienteIds)
            ->where('created_at', '>=', now()->subDay())
            ->whereNotIn('id', $readCommentIds)
            ->with(['user', 'expediente'])
            ->latest()
            ->take(5)
            ->get();
    }

    #[On('message-sent')]
    #[On('message-read')]
    public function refreshNotifications()
    {
        $this->loadUnreadCount();
        $this->loadRecentMessages();
    }

    #[On('new-message-received')]
    public function handleNewMessage()
    {
        $this->loadUnreadCount();
        $this->loadRecentMessages();
        $this->dispatch('play-notification-sound');
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($commentId)
    {
        $user = auth()->user();
        try {
            \DB::table('comentario_reads')->insertOrIgnore([
                'user_id' => $user->id,
                'comentario_id' => $commentId,
                'read_at' => now(),
            ]);
            
            $this->loadUnreadCount();
            $this->loadRecentMessages();
        } catch (\Exception $e) {
            // Ignore duplicate entry errors or others
        }
    }

    public function render()
    {
        return view('livewire.layout.messages-notification');
    }
}
