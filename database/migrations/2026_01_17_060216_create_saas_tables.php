<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabla de Planes (Paquetes)
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Básico, Pro, Enterprise
                $table->string('slug')->unique();
                $table->string('stripe_price_id')->nullable(); // ID del precio en Stripe
                $table->decimal('price', 10, 2);
                $table->integer('duration_in_days')->default(30); // 30 días por defecto
                $table->json('features')->nullable(); // Lista de características
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Configuración Global del SaaS (Solo Super Admin)
        if (!Schema::hasTable('global_settings')) {
            Schema::create('global_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique(); // ej: 'trial_days', 'grace_period_days'
                $table->string('value')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
            });

            // Insertar configuraciones por defecto solo si se crea la tabla
            DB::table('global_settings')->insert([
                ['key' => 'trial_days', 'value' => '15', 'description' => 'Días de prueba gratuita por defecto', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'grace_period_days', 'value' => '3', 'description' => 'Días extra de acceso limitado tras vencimiento', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 3. Modificar Tenants para soportar suscripciones y pruebas
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'plan_id')) {
                $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            }
            // trial_ends_at ya existe en la tabla original
            if (!Schema::hasColumn('tenants', 'subscription_ends_at')) {
                $table->timestamp('subscription_ends_at')->nullable();
            }
            if (!Schema::hasColumn('tenants', 'grace_period_ends_at')) {
                $table->timestamp('grace_period_ends_at')->nullable();
            }
            if (!Schema::hasColumn('tenants', 'subscription_status')) {
                $table->string('subscription_status')->default('trial'); // trial, active, past_due, cancelled, grace_period
            }
            if (!Schema::hasColumn('tenants', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable()->index();
            }
            if (!Schema::hasColumn('tenants', 'pm_type')) {
                $table->string('pm_type')->nullable(); // Payment Method Type (visa, mastercard)
            }
            if (!Schema::hasColumn('tenants', 'pm_last_four')) {
                $table->string('pm_last_four', 4)->nullable(); // Últimos 4 dígitos
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn([
                'plan_id', 
                'trial_ends_at', 
                'subscription_ends_at', 
                'grace_period_ends_at', 
                'subscription_status',
                'stripe_customer_id',
                'pm_type',
                'pm_last_four'
            ]);
        });

        Schema::dropIfExists('global_settings');
        Schema::dropIfExists('plans');
    }
};
