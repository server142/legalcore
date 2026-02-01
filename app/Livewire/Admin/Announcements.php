<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SystemAnnouncement;
use App\Traits\Auditable;

class Announcements extends Component
{
    use Auditable;

    public $subject;
    public $content;
    public $target = 'all';

    protected $rules = [
        'subject' => 'required|min:5|max:255',
        'content' => 'required|min:10',
        'target' => 'required|in:all,admins,superadmins'
    ];

    public function send()
    {
        $this->validate();

        $query = User::query()->where('status', 'active');

        if ($this->target === 'admins') {
            $query->role('admin');
        } elseif ($this->target === 'superadmins') {
            $query->role('super-admin');
        }

        $users = $query->get();
        $count = 0;

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->queue(new SystemAnnouncement($this->subject, $this->content));
                $count++;
            } catch (\Exception $e) {
                \Log::error("Failed to queue announcement for {$user->email}: " . $e->getMessage());
            }
        }

        $this->logAudit('notificar', 'Anuncios', "Envío masivo de anuncio: {$this->subject}", ['total_users' => $count, 'target' => $this->target]);

        session()->flash('success', "¡Anuncio enviado correctamente a {$count} destinatarios!");
        $this->reset(['subject', 'content']);
    }

    public function render()
    {
        return view('livewire.admin.announcements')->layout('layouts.app');
    }
}
