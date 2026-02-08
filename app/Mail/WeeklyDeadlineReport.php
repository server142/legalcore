<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklyDeadlineReport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $expedientes;
    public $is_admin;

    /**
     * Create a new message instance.
     * 
     * @param User $user
     * @param Collection $expedientes
     * @param bool $is_admin
     */
    public function __construct(User $user, Collection $expedientes, bool $is_admin)
    {
        $this->user = $user;
        $this->expedientes = $expedientes;
        $this->is_admin = $is_admin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $title = $this->is_admin ? 'Reporte Maestro Semanal de Vencimientos' : 'Tu Agenda Semanal de Vencimientos';
        return new Envelope(
            subject: "🗓️ {$title} - " . now()->format('d/m/Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.expedientes.weekly-report',
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
