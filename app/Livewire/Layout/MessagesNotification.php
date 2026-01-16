<?php

namespace App\Livewire\Layout;

use Livewire\Component;
use App\Models\Mensaje;
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
        $this->unreadCount = Mensaje::where('receiver_id', auth()->id())
            ->where('leido', false)
            ->count();
    }

    public function loadRecentMessages()
    {
        $this->recentMessages = Mensaje::where('receiver_id', auth()->id())
            ->where('leido', false)
            ->with('sender')
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
        $mensaje = Mensaje::find($messageId);
        if ($mensaje && $mensaje->receiver_id === auth()->id()) {
            $mensaje->update(['leido' => true]);
            $this->dispatch('message-read');
        }
    }

    public function render()
    {
        return view('livewire.layout.messages-notification');
    }
}
