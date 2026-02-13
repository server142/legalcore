<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailSettingsService
{
    /**
     * Aplica la configuración de correo desde la base de datos de forma dinámica.
     */
    public static function applySettings(): void
    {
        try {
            if (!Schema::hasTable('global_settings')) {
                return;
            }

            $mailSettings = DB::table('global_settings')
                ->where('key', 'like', 'mail_%')
                ->orWhere('key', 'resend_api_key')
                ->pluck('value', 'key')
                ->toArray();

            if (empty($mailSettings)) {
                return;
            }

            $mailer = $mailSettings['mail_mailer'] ?? config('mail.default');
            
            // Critical: Set the default mailer first
            config(['mail.default' => $mailer]);

            if ($mailer === 'smtp') {
                // Configurar SMTP exactamente como en GlobalSettings::testMail
                config([
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.host' => $mailSettings['mail_host'] ?? config('mail.mailers.smtp.host'),
                    'mail.mailers.smtp.port' => (int)($mailSettings['mail_port'] ?? config('mail.mailers.smtp.port')),
                    'mail.mailers.smtp.username' => $mailSettings['mail_username'] ?? config('mail.mailers.smtp.username'),
                    'mail.mailers.smtp.password' => $mailSettings['mail_password'] ?? config('mail.mailers.smtp.password'),
                    'mail.mailers.smtp.encryption' => ($mailSettings['mail_encryption'] ?? 'tls') === 'none' ? null : ($mailSettings['mail_encryption'] ?? 'tls'),
                    'mail.mailers.smtp.timeout' => null,
                    'mail.mailers.smtp.auth_mode' => null,
                ]);
                
                // Forzar recarga del driver SMTP
                Mail::purge('smtp');
                
            } elseif ($mailer === 'resend') {
                // Configurar Resend API
                config([
                    'mail.mailers.resend.transport' => 'resend',
                    'services.resend.key' => $mailSettings['resend_api_key'] ?? config('services.resend.key'),
                ]);
                
                // Forzar recarga del driver Resend
                Mail::purge('resend');
            }

            // Configurar remitente global
            if (!empty($mailSettings['mail_from_address'])) {
                config([
                    'mail.from.address' => $mailSettings['mail_from_address'],
                    'mail.from.name' => $mailSettings['mail_from_name'] ?? config('app.name'),
                ]);
            }
            if (isset($mailSettings['mail_from_name'])) {
                config(['mail.from.name' => $mailSettings['mail_from_name']]);
            }

        } catch (\Throwable $e) {
            Log::warning('MailSettingsService Error: ' . $e->getMessage());
        }
    }
}
