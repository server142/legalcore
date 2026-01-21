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
        // Asegurar columnas en la tabla plans
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (!Schema::hasColumn('plans', 'stripe_price_id')) {
                    $table->string('stripe_price_id')->nullable()->after('slug');
                }
            });
        }

        // Asegurar columnas en la tabla tenants
        if (Schema::hasTable('tenants')) {
            Schema::table('tenants', function (Blueprint $table) {
                if (!Schema::hasColumn('tenants', 'plan_id')) {
                    $table->foreignId('plan_id')->nullable()->after('id')->constrained('plans')->nullOnDelete();
                }
                if (!Schema::hasColumn('tenants', 'subscription_ends_at')) {
                    $table->timestamp('subscription_ends_at')->nullable()->after('trial_ends_at');
                }
                if (!Schema::hasColumn('tenants', 'grace_period_ends_at')) {
                    $table->timestamp('grace_period_ends_at')->nullable()->after('subscription_ends_at');
                }
                if (!Schema::hasColumn('tenants', 'subscription_status')) {
                    $table->string('subscription_status')->default('trial')->after('grace_period_ends_at');
                }
                if (!Schema::hasColumn('tenants', 'stripe_customer_id')) {
                    $table->string('stripe_customer_id')->nullable()->index()->after('subscription_status');
                }
                if (!Schema::hasColumn('tenants', 'pm_type')) {
                    $table->string('pm_type')->nullable()->after('stripe_customer_id');
                }
                if (!Schema::hasColumn('tenants', 'pm_last_four')) {
                    $table->string('pm_last_four', 4)->nullable()->after('pm_type');
                }
            });
        }

        // Asegurar columnas en la tabla users
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'stripe_id')) {
                    $table->string('stripe_id')->nullable()->index()->after('email');
                }
                if (!Schema::hasColumn('users', 'pm_type')) {
                    $table->string('pm_type')->nullable()->after('stripe_id');
                }
                if (!Schema::hasColumn('users', 'pm_last_four')) {
                    $table->string('pm_last_four', 4)->nullable()->after('pm_type');
                }
                if (!Schema::hasColumn('users', 'trial_ends_at')) {
                    $table->timestamp('trial_ends_at')->nullable()->after('pm_last_four');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir en una migración de parche
    }
};
