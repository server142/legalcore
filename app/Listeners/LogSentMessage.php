<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LogSentMessage
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
    public function handle(MessageSent $event): void
    {
        $message = $event->sent->getOriginalMessage();
        
        // Extract basic info
        $to = collect($message->getTo())->map(fn($addr) => $addr->getAddress())->implode(', ');
        $subject = $message->getSubject();

        // Create log entry
        AuditLog::create([
            // Fallback to ID 1 (System Admin) when running from console/scheduler
            'tenant_id' => Auth::user()?->tenant_id ?? 1,
            'user_id' => Auth::id() ?? 1,
            'accion' => 'email_sent',
            'modulo' => 'system',
            'descripcion' => "Correo enviado a: {$to}. Asunto: {$subject}",
            'metadatos' => [
                'to' => $to,
                'subject' => $subject,
                'status' => 'success'
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
