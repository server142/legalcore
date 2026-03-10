<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('plan')->default('trial')->after('name'); // trial, basico, profesional, despacho
            $table->boolean('is_active')->default(true)->after('plan');
            $table->date('subscription_ends_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['plan', 'trial_ends_at', 'is_active', 'subscription_ends_at']);
        });
    }
};
