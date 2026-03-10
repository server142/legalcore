<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class AdminFixTenantPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:fix-tenant-plan {tenant_id=5}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Restore Trial and Exempt plans and link a tenant to the trial plan.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');

        $this->info("Iniciando restauración de planes...");

        // 1. Restaurar Plan TRIAL (Prueba Gratuita)
        $trial = Plan::updateOrCreate(
            ['slug' => 'trial'],
            [
                'name' => 'Prueba Gratuita',
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
            ]
        );
        $this->info("✅ Plan Trial (Slug: trial) restaurado. ID: {$trial->id}");

        // 2. Restaurar Plan EXENTO (Cortesía)
        $exento = Plan::updateOrCreate(
            ['slug' => 'exento'],
            [
                'name' => 'Plan Cortesía / Exento',
                'stripe_price_id' => null,
                'price' => 0.00,
                'duration_in_days' => 3650,
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
                ]
            ]
        );
        $this->info("✅ Plan Exento (Slug: exento) restaurado. ID: {$exento->id}");

        // 3. Vincular el Tenant al Plan Trial
        $tenant = Tenant::find($tenantId);

        if ($tenant) {
            $tenant->update([
                'plan_id' => $trial->id,
                'plan' => 'trial',
                'is_active' => true
            ]);
            $this->info("✅ Tenant '{$tenant->name}' (ID: {$tenantId}) vinculado al plan Trial correctamente.");
        } else {
            $this->error("❌ No se encontró el Tenant con ID: {$tenantId}");
        }

        $this->info("Proceso terminado.");
        return 0;
    }
}
