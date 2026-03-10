<?php

namespace App\Mail;

use App\Models\Expediente;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpedienteAssigned extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $expediente;
    public $user;
    public $isResponsible;

    /**
     * Create a new message instance.
     */
    public function __construct(Expediente $expediente, User $user, bool $isResponsible = false)
    {
        $this->expediente = $expediente;
        $this->user = $user;
        $this->isResponsible = $isResponsible;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isResponsible 
            ? 'Asignación de Expediente Responsable: ' . $this->expediente->numero 
            : 'Nueva Asignación de Expediente: ' . $this->expediente->numero;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.expedientes.assigned',
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
