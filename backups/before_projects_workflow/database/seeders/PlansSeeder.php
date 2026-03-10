<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Básico',
                'slug' => 'basico',
                'stripe_price_id' => null, // Configurarás esto después en Stripe
                'price' => 49.99,
                'duration_in_days' => 30,
                'features' => [
                    '1 usuario administrador',
                    'Hasta 50 expedientes activos',
                    'Gestión de términos',
                    'Calendario de audiencias',
                    '5 GB de almacenamiento',
                    'Soporte por email',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Profesional',
                'slug' => 'profesional',
                'stripe_price_id' => null,
                'price' => 99.99,
                'duration_in_days' => 30,
                'features' => [
                    'Hasta 5 usuarios',
                    'Expedientes ilimitados',
                    'Gestión de términos avanzada',
                    'Calendario compartido',
                    'Mensajería interna',
                    'Facturación y reportes',
                    '50 GB de almacenamiento',
                    'Soporte prioritario',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'stripe_price_id' => null,
                'price' => 299.99,
                'duration_in_days' => 30,
                'features' => [
                    'Usuarios ilimitados',
                    'Expedientes ilimitados',
                    'Todas las funcionalidades',
                    'API personalizado',
                    'Almacenamiento ilimitado',
                    'Soporte 24/7 dedicado',
                    'Capacitación incluida',
                    'Personalización de marca',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }
}
