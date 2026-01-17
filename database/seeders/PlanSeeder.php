<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Trial',
                'slug' => 'trial',
                'stripe_price_id' => null,
                'price' => 0.00,
                'duration_in_days' => 15,
                'max_admin_users' => 1,
                'max_lawyer_users' => 1, // 1 admin + 1 abogado
                'features' => json_encode([
                    'Acceso completo por 15 días',
                    '1 usuario administrador',
                    '1 usuario abogado',
                    'Gestión de expedientes',
                    'Calendario de términos',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Paquete 2',
                'slug' => 'paquete-2',
                'stripe_price_id' => null, // Configurar después con Stripe
                'price' => 999.00,
                'duration_in_days' => 30,
                'max_admin_users' => 1,
                'max_lawyer_users' => 5, // 1 admin + 5 abogados
                'features' => json_encode([
                    'Acceso completo mensual',
                    '1 usuario administrador',
                    'Hasta 5 usuarios abogados',
                    'Gestión de expedientes',
                    'Calendario de términos',
                    'Documentos ilimitados',
                    'Reportes avanzados',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Paquete 3',
                'slug' => 'paquete-3',
                'stripe_price_id' => null, // Configurar después con Stripe
                'price' => 1999.00,
                'duration_in_days' => 30,
                'max_admin_users' => 1,
                'max_lawyer_users' => null, // 1 admin + abogados ilimitados
                'features' => json_encode([
                    'Acceso completo mensual',
                    '1 usuario administrador',
                    'Usuarios abogados ilimitados',
                    'Gestión de expedientes',
                    'Calendario de términos',
                    'Documentos ilimitados',
                    'Reportes avanzados',
                    'Soporte prioritario',
                ]),
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
