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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('user_agent')->nullable()->after('ip_address');
            $table->string('severity')->default('low')->after('user_agent'); // low, medium, high, critical
            $table->string('browser')->nullable()->after('severity');
            $table->string('os')->nullable()->after('browser');
            $table->string('device')->nullable()->after('os');
            $table->string('session_id')->nullable()->after('device');
            
            // Índices para búsquedas de seguridad rápidas
            $table->index('severity');
            $table->index('ip_address');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['user_agent', 'severity', 'browser', 'os', 'device', 'session_id']);
        });
    }
};
