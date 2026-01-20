<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);

        // Override Mail Config from DB
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('global_settings')) {
                // Mail Settings
                $mailSettings = \Illuminate\Support\Facades\DB::table('global_settings')
                    ->where('key', 'like', 'mail_%')
                    ->pluck('value', 'key')
                    ->toArray();

                if (!empty($mailSettings)) {
                    config([
                        'mail.mailers.smtp.host' => $mailSettings['mail_host'] ?? config('mail.mailers.smtp.host'),
                        'mail.mailers.smtp.port' => $mailSettings['mail_port'] ?? config('mail.mailers.smtp.port'),
                        'mail.mailers.smtp.encryption' => $mailSettings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
                        'mail.mailers.smtp.username' => $mailSettings['mail_username'] ?? config('mail.mailers.smtp.username'),
                        'mail.mailers.smtp.password' => $mailSettings['mail_password'] ?? config('mail.mailers.smtp.password'),
                        'mail.from.address' => $mailSettings['mail_from_address'] ?? config('mail.from.address'),
                        'mail.from.name' => $mailSettings['mail_from_name'] ?? config('mail.from.name'),
                    ]);
                }

                // Stripe Settings
                $stripeSettings = \Illuminate\Support\Facades\DB::table('global_settings')
                    ->whereIn('key', ['stripe_key', 'stripe_secret', 'stripe_webhook_secret'])
                    ->pluck('value', 'key')
                    ->toArray();

                if (!empty($stripeSettings)) {
                    if (isset($stripeSettings['stripe_key'])) {
                        config(['cashier.key' => $stripeSettings['stripe_key']]);
                    }
                    if (isset($stripeSettings['stripe_secret'])) {
                        config(['cashier.secret' => $stripeSettings['stripe_secret']]);
                    }
                    if (isset($stripeSettings['stripe_webhook_secret'])) {
                        config(['cashier.webhook.secret' => $stripeSettings['stripe_webhook_secret']]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fail silently if DB not ready or any error occurs
            \Illuminate\Support\Facades\Log::warning('AppServiceProvider boot error: ' . $e->getMessage());
        }
    }
}
