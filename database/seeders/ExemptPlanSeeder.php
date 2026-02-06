<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class ExemptPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::updateOrCreate(
            ['slug' => 'exento'],
            [
                'name' => 'Plan Cortesía / Exento',
                'stripe_price_id' => null,
                'price' => 0.00,
                'duration_in_days' => 3650, // 10 años aprox
                'max_admin_users' => 10,
                'max_lawyer_users' => 50,
                'max_expedientes' => 10000,
                'storage_limit_gb' => 100,
                'is_active' => true,
                'features' => [
                    'Acceso total de por vida',
                    'Asistente IA ilimitado',
                    'Soporte prioritario',
                    'Multiusuario avanzado',
                    'Almacenamiento extendido'
                ],
            ]
        );
    }
}
