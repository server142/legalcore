<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UserCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $subjectStr;
    public $bodyStr;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $password = null)
    {
        $this->user = $user;
        $this->password = $password;

        // Load settings from DB
        $settings = DB::table('global_settings')
            ->whereIn('key', ['mail_user_welcome_subject', 'mail_user_welcome_body'])
            ->pluck('value', 'key');

        $this->subjectStr = $settings['mail_user_welcome_subject'] ?? 'Bienvenido a Diogenes - Tu despacho en la nube';
        $this->bodyStr = $settings['mail_user_welcome_body'] ?? "¡Bienvenido a bordo {nombre}!\n\nEstamos emocionados de tenerte con nosotros.";

        // Replace placeholders
        $replacements = [
            '{nombre}' => $user->name,
            '{email}' => $user->email,
            '{password}' => $password ?? '****** (la que elegiste al registrarte)',
        ];

        $this->subjectStr = str_replace(array_keys($replacements), array_values($replacements), $this->subjectStr);
        $this->bodyStr = str_replace(array_keys($replacements), array_values($replacements), $this->bodyStr);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectStr,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.users.created',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
