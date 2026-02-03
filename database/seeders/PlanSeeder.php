<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BACKUP DE SEGURIDAD (Caja Negra)
        $oldPlans = Plan::all();
        $backupName = 'plans_backup_' . date('Ymd_His') . '.json';
        Storage::disk('local')->put($backupName, $oldPlans->toJson(JSON_PRETTY_PRINT));

        // 2. LIMPIEZA SEGURA
        // Desactivamos temporalmente el chequeo de llaves foráneas para limpiar y reconstruir
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Plan::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 3. CREACIÓN DE PLANES OFICIALES
        
        // --- PLAN TRIAL ---
        $trial = Plan::create([
            'name' => 'Prueba Gratuita',
            'slug' => 'trial',
            'stripe_price_id' => null,
            'price' => 0.00,
            'duration_in_days' => 14,
            'max_admin_users' => 1,
            'max_lawyer_users' => 1,
            'max_expedientes' => 10,
            'storage_limit_gb' => 1,
            'is_active' => true,
            'features' => [
                'Acceso total por 14 días',
                'Gestión de 10 expedientes',
                'Asistente IA básico',
                'Soporte vía Ticket'
            ]
        ]);

        // --- PLAN BÁSICO ---
        $basico = Plan::create([
            'name' => 'Plan Básico',
            'slug' => 'basico',
            'stripe_price_id' => null,
            'price' => 800.00,
            'duration_in_days' => 30,
            'max_admin_users' => 1,
            'max_lawyer_users' => 1,
            'max_expedientes' => 50,
            'storage_limit_gb' => 5,
            'is_active' => true,
            'features' => [
                'Gestión de Expedientes y Clientes',
                'Agenda Jurídica Integrada',
                'Términos Procesales con Alertas',
                'Almacenamiento Seguro (5GB)',
                'Bitácora de Seguridad'
            ]
        ]);

        // --- PLAN PROFESIONAL ---
        $profesional = Plan::create([
            'name' => 'Plan Profesional',
            'slug' => 'profesional',
            'stripe_price_id' => null,
            'price' => 1200.00,
            'duration_in_days' => 30,
            'max_admin_users' => 1,
            'max_lawyer_users' => 10,
            'max_expedientes' => 500,
            'storage_limit_gb' => 20,
            'is_active' => true,
            'features' => [
                'Todo lo del Plan Básico',
                'Asistente Inteligente (IA) Avanzado',
                'Gestión de Honorarios y Facturación',
                'Reportes de Rendimiento',
                'Almacenamiento Ampliado (20GB)',
                'Soporte Prioritario'
            ]
        ]);

        // --- PLAN ENTERPRISE ---
        $enterprise = Plan::create([
            'name' => 'Plan Enterprise',
            'slug' => 'enterprise',
            'stripe_price_id' => null,
            'price' => 1999.00,
            'duration_in_days' => 30,
            'max_admin_users' => 3,
            'max_lawyer_users' => null, // Ilimitado
            'max_expedientes' => 0, // Ilimitado (según lógica de la app)
            'storage_limit_gb' => 100,
            'is_active' => true,
            'features' => [
                'Todo lo del Plan Profesional',
                'Expedientes y Abogados Ilimitados',
                'IA de Alto Rendimiento',
                'Api Access / Integraciones',
                'Personalización de Marca (White Label)',
                '100GB de Almacenamiento',
                'Atención Dedicada'
            ]
        ]);

        // 4. RECONCILIACIÓN DE TENANTS (Evitar que se rompa el acceso)
        $tenants = Tenant::all();
        foreach($tenants as $tenant) {
            $matchingPlan = match($tenant->plan) {
                'trial' => $trial,
                'basico' => $basico,
                'profesional' => $profesional,
                'enterprise' => $enterprise,
                default => null
            };

            if($matchingPlan) {
                $tenant->update(['plan_id' => $matchingPlan->id]);
            }
        }
        
        echo "Seeder ejecutado con éxito.\n";
        echo "- Precios actualizados: Básico (800), Profesional (1200), Enterprise (1999).\n";
        echo "- Backup guardado: storage/app/{$backupName}\n";
        echo "- Despachos existentes sincronizados correctamente.\n";
    }
}
