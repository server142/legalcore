<?php

namespace App\Mail;

use App\Models\Asesoria;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AsesoriaNotificationAbogado extends Mailable implements ShouldQueue
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
        // La URL pública es útil también para el abogado para ver la "ficha" rápidamente
        $this->publicUrl = route('asesorias.public', ['token' => $asesoria->public_token]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Asesoría Asignada - Folio: ' . $this->asesoria->folio,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Reutilizamos la misma vista que el cliente pero el contexto cambia en el asunto
        // y el abogado entenderá que es su copia de la ficha.
        // Si se requiere algo muy diferente, se debe crear emails.asesorias.notification_abogado
        return new Content(
            markdown: 'emails.asesorias.notification_abogado',
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
