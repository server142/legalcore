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
        try {
            $user = auth()->user();
            $expedienteIds = Expediente::where('abogado_responsable_id', $user->id)
                ->orWhereHas('assignedUsers', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->pluck('id');

            // Check if table exists before querying
            if (\Schema::hasTable('comentario_reads')) {
                $readCommentIds = \DB::table('comentario_reads')
                    ->where('user_id', $user->id)
                    ->pluck('comentario_id');
            } else {
                $readCommentIds = collect([]);
            }

            $this->unreadCount = Comentario::whereIn('expediente_id', $expedienteIds)
                ->where('created_at', '>=', now()->subDay())
                ->whereNotIn('id', $readCommentIds)
                ->count();
        } catch (\Exception $e) {
            $this->unreadCount = 0;
        }
    }

    public function loadRecentMessages()
    {
        try {
            $user = auth()->user();
            $expedienteIds = Expediente::where('abogado_responsable_id', $user->id)
                ->orWhereHas('assignedUsers', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->pluck('id');

            // Check if table exists before querying
            if (\Schema::hasTable('comentario_reads')) {
                $readCommentIds = \DB::table('comentario_reads')
                    ->where('user_id', $user->id)
                    ->pluck('comentario_id');
            } else {
                $readCommentIds = collect([]);
            }

            $this->recentMessages = Comentario::whereIn('expediente_id', $expedienteIds)
                ->where('created_at', '>=', now()->subDay())
                ->whereNotIn('id', $readCommentIds)
                ->with(['user', 'expediente'])
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            $this->recentMessages = collect([]);
        }
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
        try {
            // Only mark as read if table exists
            if (!\Schema::hasTable('comentario_reads')) {
                return;
            }

            $user = auth()->user();
            \DB::table('comentario_reads')->insertOrIgnore([
                'user_id' => $user->id,
                'comentario_id' => $commentId,
                'read_at' => now(),
            ]);
            
            $this->loadUnreadCount();
            $this->loadRecentMessages();
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist
        }
    }

    public function render()
    {
        return view('livewire.layout.messages-notification');
    }
}
