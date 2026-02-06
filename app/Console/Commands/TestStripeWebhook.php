<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\Plan;
use Laravel\Cashier\Events\WebhookHandled;

class TestStripeWebhook extends Command
{
    protected $signature = 'test:stripe-webhook {tenant_id} {event=invoice.payment_succeeded}';
    protected $description = 'Simulate a Stripe Webhook for testing';

    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        $eventName = $this->argument('event');

        $tenant = Tenant::findOrFail($tenantId);
        
        if (!$tenant->stripe_customer_id) {
            $this->error("Tenant does not have a stripe_customer_id");
            return;
        }

        $plan = $tenant->planRelation ?? Plan::where('slug', $tenant->plan)->first();
        
        $payload = [
            'id' => 'evt_test_' . time(),
            'type' => $eventName,
            'data' => [
                'object' => [
                    'object' => 'invoice',
                    'customer' => $tenant->stripe_customer_id,
                    'lines' => [
                        'data' => [
                            [
                                'period' => [
                                    'end' => now()->addMonth()->getTimestamp()
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        if ($eventName === 'customer.subscription.deleted') {
            $payload['data']['object']['object'] = 'subscription';
            $payload['data']['object']['status'] = 'canceled';
        }

        $this->info("Simulating webhook: {$eventName} for tenant: {$tenant->name}");

        event(new WebhookHandled($payload));

        $tenant->refresh();
        $this->info("New status: " . $tenant->subscription_status);
        $this->info("Ends at: " . $tenant->subscription_ends_at);
        
        if ($tenant->subscription_status === 'active') {
            $this->info("SUCCESS: Tenant updated correctly.");
        }
    }
}
