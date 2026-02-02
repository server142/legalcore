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
            config(['mail.default' => $mailer]);

            if ($mailer === 'smtp') {
                config([
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.host' => $mailSettings['mail_host'] ?? config('mail.mailers.smtp.host'),
                    'mail.mailers.smtp.port' => (int)($mailSettings['mail_port'] ?? config('mail.mailers.smtp.port')),
                    'mail.mailers.smtp.username' => $mailSettings['mail_username'] ?? config('mail.mailers.smtp.username'),
                    'mail.mailers.smtp.password' => $mailSettings['mail_password'] ?? config('mail.mailers.smtp.password'),
                ]);

                if (isset($mailSettings['mail_encryption'])) {
                    $enc = $mailSettings['mail_encryption'] === 'none' ? null : $mailSettings['mail_encryption'];
                    config(['mail.mailers.smtp.encryption' => $enc]);
                }
                
                Mail::purge('smtp');
            } elseif ($mailer === 'resend') {
                config([
                    'mail.mailers.resend.transport' => 'resend',
                    'services.resend.key' => $mailSettings['resend_api_key'] ?? config('services.resend.key'),
                ]);
                
                Mail::purge('resend');
            }

            if (isset($mailSettings['mail_from_address'])) {
                config(['mail.from.address' => $mailSettings['mail_from_address']]);
            }
            if (isset($mailSettings['mail_from_name'])) {
                config(['mail.from.name' => $mailSettings['mail_from_name']]);
            }

        } catch (\Throwable $e) {
            Log::warning('MailSettingsService Error: ' . $e->getMessage());
        }
    }
}
