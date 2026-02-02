<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\MailSettingsService;

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
        Event::subscribe(\App\Listeners\AuthEventsSubscriber::class);

        \App\Models\Evento::observe(\App\Observers\EventoObserver::class);

        Schema::defaultStringLength(191);

        try {
            if (Schema::hasTable('global_settings')) {
                // Dynamic Mail Settings
                MailSettingsService::applySettings();

                // Stripe Settings
                $stripeSettings = DB::table('global_settings')
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

                // File Upload Settings
                $maxFileSizeSetting = DB::table('global_settings')
                    ->where('key', 'max_file_size_mb')
                    ->value('value');

                $maxFileSize = is_numeric($maxFileSizeSetting) ? (int)$maxFileSizeSetting : 100;
                if ($maxFileSize <= 0) $maxFileSize = 100;

                $maxFileSizeInKb = $maxFileSize * 1024;
                config([
                    'livewire.temporary_file_upload.rules' => ['required', 'file', "max:{$maxFileSizeInKb}"]
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('AppServiceProvider boot error: ' . $e->getMessage());
        }
    }
}
