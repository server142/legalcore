<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de eventos individuales de analytics
        Schema::create('directory_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('directory_profile_id')->constrained()->cascadeOnDelete();
            $table->enum('event_type', ['profile_view', 'search_impression', 'whatsapp_click', 'share_click']);
            $table->string('ip_address', 45)->nullable();
            $table->string('session_id')->nullable();
            $table->string('search_query')->nullable(); // Para search impressions
            $table->date('event_date'); // Para agrupar por día fácilmente
            $table->timestamps();

            $table->index(['directory_profile_id', 'event_type', 'event_date'], 'dir_analytics_profile_type_date_idx');
        });

        // Tabla de pagos del directorio
        Schema::create('directory_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('directory_profile_id')->constrained()->cascadeOnDelete();
            $table->string('plan'); // 'directory-free', 'directory-basic', 'directory-premium'
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('MXN');
            $table->enum('status', ['paid', 'pending', 'cancelled', 'refunded'])->default('pending');
            $table->string('reference')->nullable(); // Referencia de pago
            $table->string('method')->nullable(); // 'stripe', 'oxxo', 'transfer'
            $table->timestamp('paid_at')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->timestamps();
        });

        // Agregar columnas adicionales a directory_profiles para stats acumuladas
        Schema::table('directory_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('search_impressions_count')->default(0)->after('contact_clicks_count');
            $table->unsignedBigInteger('share_clicks_count')->default(0)->after('search_impressions_count');
            $table->string('plan')->default('directory-free')->after('is_verified');
            $table->timestamp('plan_expires_at')->nullable()->after('plan');
        });
    }

    public function down(): void
    {
        Schema::table('directory_profiles', function (Blueprint $table) {
            $table->dropColumn(['search_impressions_count', 'share_clicks_count', 'plan', 'plan_expires_at']);
        });
        Schema::dropIfExists('directory_payments');
        Schema::dropIfExists('directory_analytics');
    }
};
