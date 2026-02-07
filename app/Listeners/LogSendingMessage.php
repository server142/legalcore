<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LogSendingMessage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSending $event): void
    {
        $message = $event->message;
        
        $to = collect($message->getTo())->map(fn($addr) => $addr->getAddress())->implode(', ');
        $subject = $message->getSubject();

        AuditLog::create([
            'tenant_id' => Auth::user()?->tenant_id ?? null,
            'user_id' => Auth::id() ?? null,
            'accion' => 'email_attempt',
            'modulo' => 'system',
            'descripcion' => "Intento de envío de correo a: {$to}. Asunto: {$subject}",
            'metadatos' => [
                'to' => $to,
                'subject' => $subject,
                'status' => 'pending'
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
