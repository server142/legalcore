<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $verificationUrl;

    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
        
        $this->verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addDays(3),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a ' . config('app.name') . ' - Datos de tu cuenta',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.users.created',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
