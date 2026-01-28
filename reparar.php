<?php
// reparar.php
// Este script repara la base de datos de forma segura

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Plan;

try {
    echo "\n--- INICIANDO REPARACIÓN DE SISTEMA ---\n";

    // 1. Reparar tabla tenants
    echo "Verificando tabla 'tenants'... ";
    if (!Schema::hasColumn('tenants', 'deleted_at')) {
        Schema::table('tenants', function (Blueprint $table) {
            $table->softDeletes();
        });
        echo "✅ Columna 'deleted_at' agregada.\n";
    } else {
        echo "ℹ️ Ya tiene 'deleted_at'.\n";
    }

    // 2. Reparar Plan Trial
    echo "Verificando Plan 'Trial'... ";
    $trial = Plan::where('slug', 'trial')->first();
    if (!$trial) {
        Plan::create([
            'name' => 'Trial',
            'slug' => 'trial',
            'price' => 0,
            'duration_in_days' => 15,
            'max_admin_users' => 1,
            'max_lawyer_users' => 1,
            'max_expedientes' => 0,
            'storage_limit_gb' => 1,
            'features' => ['Acceso gratuito'],
            'is_active' => true
        ]);
        echo "✅ Plan 'Trial' creado.\n";
    } else {
        echo "ℹ️ El plan 'Trial' ya existe.\n";
    }

    // 3. Reparar tabla plans
    echo "Verificando tabla 'plans'... ";
    if (!Schema::hasColumn('plans', 'max_expedientes')) {
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('max_expedientes')->default(0);
        });
        echo "✅ Columna 'max_expedientes' agregada.\n";
    } else {
        echo "ℹ️ Ya tiene 'max_expedientes'.\n";
    }

    echo "\n--- REPARACIÓN FINALIZADA CON ÉXITO ---\n";
    echo "Ya puedes intentar registrarte de nuevo.\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR DURANTE LA REPARACIÓN: " . $e->getMessage() . "\n";
}
