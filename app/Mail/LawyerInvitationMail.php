<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class LawyerInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $subjectStr;
    public $bodyStr;
    public $despachoName;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->despachoName = $user->tenant ? $user->tenant->name : 'Nuestra Firma';

        // Load settings from DB
        $settings = DB::table('global_settings')
            ->whereIn('key', ['mail_lawyer_invitation_subject', 'mail_lawyer_invitation_body'])
            ->pluck('value', 'key');

        $this->subjectStr = $settings['mail_lawyer_invitation_subject'] ?? 'Invitación al Despacho {despacho} - Diogenes';
        $this->bodyStr = $settings['mail_lawyer_invitation_body'] ?? "¡Hola {nombre}!\n\nHas sido invitado a colaborar en el despacho **{despacho}**.";

        // Replace placeholders
        $replacements = [
            '{nombre}' => $user->name,
            '{email}' => $user->email,
            '{password}' => $password,
            '{despacho}' => $this->despachoName,
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
            markdown: 'emails.lawyers.invitation',
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
