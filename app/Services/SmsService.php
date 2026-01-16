<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Actuacion;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Check for upcoming terms and send SMS notifications.
     */
    public function checkAndSendNotifications()
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $settings = $tenant->settings ?? [];
            
            if (!($settings['sms_enabled'] ?? false)) {
                continue;
            }

            $daysBefore = $settings['sms_days_before'] ?? 3;
            $recipients = array_filter(array_map('trim', explode(',', $settings['sms_recipients'] ?? '')));

            if (empty($recipients)) {
                continue;
            }

            // Find terms expiring in exactly $daysBefore days
            $targetDate = now()->addDays($daysBefore)->toDateString();
            
            $terminos = Actuacion::where('tenant_id', $tenant->id)
                ->where('es_plazo', true)
                ->where('estado', 'pendiente')
                ->whereDate('fecha_vencimiento', $targetDate)
                ->get();

            foreach ($terminos as $termino) {
                $message = "AVISO LEGAL ({$tenant->name}): El término '{$termino->titulo}' del expediente {$termino->expediente->numero} vence en {$daysBefore} días ({$termino->fecha_vencimiento->format('d/m/Y')}).";
                
                foreach ($recipients as $phone) {
                    $this->sendSms($phone, $message);
                }
            }
        }
    }

    /**
     * Send an SMS message.
     * Replace this with actual provider logic (Twilio, Infobip, etc.)
     */
    protected function sendSms($to, $message)
    {
        Log::info("Enviando SMS a {$to}: {$message}");
        
        // Example Twilio integration (commented out):
        /*
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');
        $client = new \Twilio\Rest\Client($sid, $token);
        $client->messages->create($to, ['from' => $from, 'body' => $message]);
        */
    }
}
