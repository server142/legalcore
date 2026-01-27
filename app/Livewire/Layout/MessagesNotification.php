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
        // Count comments from the last 24 hours on expedientes the user is involved in
        $user = auth()->user();
        $expedienteIds = Expediente::where('abogado_responsable_id', $user->id)
            ->orWhereHas('assignedUsers', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->pluck('id');

        $this->unreadCount = Comentario::whereIn('expediente_id', $expedienteIds)
            ->where('created_at', '>=', now()->subDay())
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

        $this->recentMessages = Comentario::whereIn('expediente_id', $expedienteIds)
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

    public function markAsRead($messageId)
    {
        // No-op for now as we don't track read status for comments
    }

    public function render()
    {
        return view('livewire.layout.messages-notification');
    }
}
