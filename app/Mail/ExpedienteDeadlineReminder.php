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

class ExpedienteDeadlineReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $expediente;
    public $user;
    public $timeLabel;

    /**
     * Create a new message instance.
     */
    public function __construct(Expediente $expediente, User $recipient, string $timeLabel)
    {
        $this->expediente = $expediente;
        $this->user = $recipient;
        $this->timeLabel = $timeLabel;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $urgency = ($this->timeLabel === '24 horas' || $this->timeLabel === '12 horas') ? '⚠️ URGENTE: ' : 'RECORATORIO: ';
        return new Envelope(
            subject: "{$urgency}Vencimiento de Término ({$this->timeLabel}) - Exp. {$this->expediente->numero}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.expedientes.deadline-reminder',
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
