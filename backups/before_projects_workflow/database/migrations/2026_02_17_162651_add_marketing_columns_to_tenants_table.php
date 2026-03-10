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
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('has_marketing_module')->default(false)->after('id'); // Si el despacho compró el "Skill"
            $table->integer('marketing_credits')->default(5)->after('has_marketing_module'); // Saldo de imágenes (5 de prueba gratis)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['has_marketing_module', 'marketing_credits']);
        });
    }
};
