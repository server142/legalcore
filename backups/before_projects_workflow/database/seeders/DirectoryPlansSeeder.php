<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class DirectoryPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Plan Gratuito (Básico)
        Plan::updateOrCreate(
            ['slug' => 'directory-free'],
            [
                'name' => 'Perfil Básico (Gratis)',
                'price' => 0.00,
                'interval' => 'monthly', // Aunque sea gratis, mantenemos la estructura
                'features' => [
                    'directory_listing' => true,
                    'verified_badge' => false,
                    'whatsapp_button' => false,
                    'priority_support' => false,
                    'case_management' => false,
                    'ai_assistant' => false,
                ],
                'description' => 'Ideal para tener presencia básica en el directorio.',
                'is_active' => true,
                'stripe_id' => 'price_free_tier_placeholder', // Placeholder o vacío
                'duration_in_days' => 3650, // 10 años, "ilimitado"
            ]
        );

        // 2. Plan Directorio Destacado (Pago)
        Plan::updateOrCreate(
            ['slug' => 'directory-only'],
            [
                'name' => 'Perfil Destacado',
                'price' => 199.00,
                'interval' => 'monthly',
                'features' => [
                    'directory_listing' => true,
                    'verified_badge' => true,
                    'whatsapp_button' => true,
                    'priority_support' => true,
                    'search_priority' => true,
                    'case_management' => false,
                    'ai_assistant' => false,
                ],
                'description' => 'Aumenta tu visibilidad y recibe clientes directamente.',
                'is_active' => true,
                'stripe_id' => 'price_directory_premium_placeholder', // Actualizar con ID real de Stripe
                'duration_in_days' => 30,
            ]
        );

        $this->command->info('Planes de Directorio (Gratis y Destacado) creados o actualizados correctamente.');
    }
}
