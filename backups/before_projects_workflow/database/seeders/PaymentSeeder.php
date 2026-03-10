<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = \App\Models\Tenant::all();
        $plans = \App\Models\Plan::all();

        if ($tenants->isEmpty() || $plans->isEmpty()) {
            return;
        }

        foreach ($tenants as $tenant) {
            // Crear 3 pagos para cada tenant en los últimos 3 meses
            for ($i = 0; $i < 3; $i++) {
                $plan = $plans->random();
                if ($plan->slug === 'trial') continue;

                \App\Models\Payment::create([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                    'amount' => $plan->price,
                    'currency' => 'MXN',
                    'status' => 'completed',
                    'payment_date' => now()->subMonths($i)->subDays(rand(1, 20)),
                ]);
            }
        }
    }
}
