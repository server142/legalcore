<?php

namespace App\Listeners;

use Laravel\Cashier\Events\WebhookHandled;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Support\Facades\Log;

class StripeWebhookListener
{
    /**
     * Handle the event.
     */
    public function handle(WebhookHandled $event): void
    {
        $payload = $event->payload;
        $type = $payload['type'];

        Log::info("Stripe Webhook received in Listener: {$type}");

        // Solo procesamos eventos relevantes para sincronizar el estado del Tenant
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
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($tenant);
                break;
        }
    }

    protected function syncSubscriptionActive(Tenant $tenant, $stripeObject)
    {
        // Si el objeto es una factura, necesitamos obtener la suscripción asociada o la fecha de la factura
        $endsAt = null;

        if ($stripeObject['object'] === 'subscription') {
            $endsAt = \Illuminate\Support\Carbon::createFromTimestamp($stripeObject['current_period_end']);
            $priceId = $stripeObject['items']['data'][0]['price']['id'] ?? null;
            
            if ($priceId) {
                $plan = Plan::where('stripe_price_id', $priceId)->first();
                if ($plan) {
                    $tenant->plan = $plan->slug;
                    $tenant->plan_id = $plan->id;
                }
            }
        } elseif ($stripeObject['object'] === 'invoice' && isset($stripeObject['lines']['data'][0]['period']['end'])) {
            $endsAt = \Illuminate\Support\Carbon::createFromTimestamp($stripeObject['lines']['data'][0]['period']['end']);
        }

        $tenant->update([
            'subscription_status' => 'active',
            'subscription_ends_at' => $endsAt,
            'is_active' => true,
        ]);

        Log::info("Tenant {$tenant->name} synchronized via Webhook ({$tenant->subscription_status}) until {$endsAt}");
    }

    protected function handleSubscriptionDeleted(Tenant $tenant)
    {
        $tenant->update([
            'subscription_status' => 'cancelled',
            'is_active' => false, // Opcional: podrías dejarlo activo hasta que venza la fecha
        ]);

        Log::info("Tenant {$tenant->name} subscription cancelled via Webhook");
    }
}
