<?php

namespace App\Listeners;

use Laravel\Cashier\Events\WebhookHandled;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\DirectoryPayment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class StripeWebhookListener
{
    /**
     * Handle the event.
     */
    public function handle(WebhookHandled $event): void
    {
        $payload = $event->payload;
        $type    = $payload['type'];

        Log::info("Stripe Webhook received in Listener: {$type}");

        $relevantEvents = [
            'customer.subscription.created',
            'customer.subscription.updated',
            'customer.subscription.deleted',
            'invoice.payment_succeeded',
        ];

        if (!in_array($type, $relevantEvents)) {
            return;
        }

        $stripeId = $payload['data']['object']['customer'] ?? null;

        if (!$stripeId) {
            return;
        }

        $tenant = Tenant::where('stripe_customer_id', $stripeId)->first();

        if (!$tenant) {
            Log::warning("Tenant not found for Stripe Customer ID: {$stripeId}");
            return;
        }

        switch ($type) {
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
            case 'invoice.payment_succeeded':
                $this->syncSubscriptionActive($tenant, $payload['data']['object']);
                // Solo para facturas pagadas: registrar en directorio si aplica
                if ($type === 'invoice.payment_succeeded') {
                    $this->recordDirectoryPaymentIfApplicable($tenant, $payload['data']['object']);
                }
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($tenant);
                break;
        }
    }

    protected function syncSubscriptionActive(Tenant $tenant, $stripeObject): void
    {
        $endsAt = null;

        if ($stripeObject['object'] === 'subscription') {
            $endsAt  = Carbon::createFromTimestamp($stripeObject['current_period_end']);
            $priceId = $stripeObject['items']['data'][0]['price']['id'] ?? null;

            if ($priceId) {
                $plan = Plan::where('stripe_price_id', $priceId)->first();
                if ($plan) {
                    $tenant->plan    = $plan->slug;
                    $tenant->plan_id = $plan->id;
                }
            }
        } elseif ($stripeObject['object'] === 'invoice' && isset($stripeObject['lines']['data'][0]['period']['end'])) {
            $endsAt = Carbon::createFromTimestamp($stripeObject['lines']['data'][0]['period']['end']);
        }

        $tenant->update([
            'subscription_status' => 'active',
            'subscription_ends_at' => $endsAt,
            'is_active'           => true,
        ]);

        Log::info("Tenant {$tenant->name} synchronized via Webhook ({$tenant->subscription_status}) until {$endsAt}");
    }

    /**
     * Registra el pago en la tabla directory_payments
     * únicamente cuando el plan del tenant es de directorio.
     * No afecta el flujo del sistema existente.
     */
    protected function recordDirectoryPaymentIfApplicable(Tenant $tenant, $invoice): void
    {
        if (!str_contains($tenant->plan ?? '', 'directory')) {
            return;
        }

        $user = $tenant->users()->first();
        if (!$user || !$user->directoryProfile) {
            return;
        }

        $profile   = $user->directoryProfile;
        $invoiceId = $invoice['id'] ?? null;

        // Evitar duplicados por referencia de invoice
        if ($invoiceId && $profile->payments()->where('reference', $invoiceId)->exists()) {
            Log::info("DirectoryPayment ya existe para invoice {$invoiceId}, omitiendo.");
            return;
        }

        $amount      = ($invoice['amount_paid'] ?? 0) / 100;
        $currency    = strtoupper($invoice['currency'] ?? 'mxn');
        $periodStart = isset($invoice['lines']['data'][0]['period']['start'])
            ? Carbon::createFromTimestamp($invoice['lines']['data'][0]['period']['start'])->toDateString()
            : null;
        $periodEnd   = isset($invoice['lines']['data'][0]['period']['end'])
            ? Carbon::createFromTimestamp($invoice['lines']['data'][0]['period']['end'])->toDateString()
            : null;

        $profile->payments()->create([
            'plan'         => $tenant->plan,
            'amount'       => $amount,
            'currency'     => $currency,
            'status'       => 'paid',
            'reference'    => $invoiceId,
            'method'       => 'stripe',
            'paid_at'      => now(),
            'period_start' => $periodStart,
            'period_end'   => $periodEnd,
        ]);

        Log::info("DirectoryPayment registrado — perfil #{$profile->id}, plan {$tenant->plan}, \${$amount} {$currency}");
    }

    protected function handleSubscriptionDeleted(Tenant $tenant): void
    {
        $tenant->update([
            'subscription_status' => 'cancelled',
            'is_active'           => false,
        ]);

        Log::info("Tenant {$tenant->name} subscription cancelled via Webhook");
    }
}
