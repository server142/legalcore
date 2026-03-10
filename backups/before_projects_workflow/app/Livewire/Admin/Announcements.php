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

    public $subject = '';
    public $content = '';
    public $target = 'all';
    public $testEmail = '';

    protected $rules = [
        'subject' => 'required|min:5|max:255',
        'content' => 'required|min:10',
        'target' => 'required|in:all,admins,superadmins',
        'testEmail' => 'nullable|email'
    ];

    public function sendTest()
    {
        $this->validate([
            'subject' => 'required|min:5|max:255',
            'content' => 'required|min:10',
            'testEmail' => 'nullable|email'
        ]);

        $recipient = !empty($this->testEmail) ? $this->testEmail : auth()->user()->email;
        
        \Log::info("ANNOUNCEMENT: Attempting TEST send to: {$recipient}. Mail Driver: " . config('mail.default') . ", Queue: " . config('queue.default'));

        try {
            Mail::to($recipient)->send(new SystemAnnouncement($this->subject, $this->content));
            \Log::info("ANNOUNCEMENT: TEST send SUCCESS to: {$recipient}");
            session()->flash('success', "¡Correo de prueba enviado a {$recipient}!");
        } catch (\Exception $e) {
            \Log::error("ANNOUNCEMENT: TEST send FAILED to: {$recipient}. Error: " . $e->getMessage());
            session()->flash('error', "Error al enviar la prueba: " . $e->getMessage());
        }
    }

    public function send()
    {
        $this->validate();
        
        \Log::info("ANNOUNCEMENT: Starting MASS SEND. Target: {$this->target}, Subject: {$this->subject}");

        $query = User::query()->where('status', 'active');

        if ($this->target === 'admins') {
            $query->role('admin');
        } elseif ($this->target === 'superadmins') {
            $query->role('super_admin');
        }

        $users = $query->get();
        $count = 0;
        $failCount = 0;
        $sentTo = [];
        $failedTo = [];

        foreach ($users as $user) {
            try {
                \Log::info("ANNOUNCEMENT: Queuing email for user: {$user->email}");
                Mail::to($user->email)->queue(new SystemAnnouncement($this->subject, $this->content));
                $count++;
                $sentTo[] = $user->email;
            } catch (\Exception $e) {
                $failCount++;
                $failedTo[] = "{$user->email} (" . $e->getMessage() . ")";
                \Log::error("ANNOUNCEMENT: FAILED to queue for {$user->email}: " . $e->getMessage());
            }
        }

        \Log::info("ANNOUNCEMENT: MASS SEND Finished. Success: {$count}, Failed: {$failCount}");

        $this->logAudit('notificar', 'Anuncios', "Envío masivo de anuncio: {$this->subject}", [
            'total_users' => $count, 
            'target' => $this->target,
            'destinatarios' => $sentTo,
            'fallidos' => $failedTo
        ]);

        if ($failCount > 0) {
            session()->flash('warning', "Anuncio procesado. Éxitos: {$count}, Fallidos: {$failCount}. Revisa los logs.");
        } else {
            session()->flash('success', "¡Anuncio enviado correctamente a {$count} destinatarios!");
        }
        
        $this->reset(['subject', 'content']);
    }

    public function render()
    {
        return view('livewire.admin.announcements')->layout('layouts.app');
    }
}
