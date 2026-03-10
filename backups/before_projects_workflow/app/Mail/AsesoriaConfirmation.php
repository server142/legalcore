<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Asesoria;

class AsesoriaConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $asesoria;
    public $publicUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Asesoria $asesoria)
    {
        $this->asesoria = $asesoria;
        $this->publicUrl = route('asesorias.public', ['token' => $asesoria->public_token]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de su Cita Legal - Diógenes',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.asesorias.confirmation',
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
