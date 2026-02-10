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
        Schema::create('tenant_billing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            
            // Datos Fiscales Mexicanos (SAT)
            $table->string('razon_social'); // Nombre o Razón Social
            $table->string('rfc', 13); // RFC (Persona Física o Moral)
            $table->string('regimen_fiscal'); // Clave del Régimen Fiscal (ej: 601, 626)
            $table->string('codigo_postal', 5); // CP Fiscal
            $table->string('direccion_fiscal')->nullable(); // Calle, No, Colonia, etc.
            $table->string('uso_cfdi')->default('G03'); // Gastos en general
            $table->string('email_facturacion')->nullable(); // Para envío de XML/PDF
            
            // Metadatos
            $table->boolean('verified')->default(false); // Para lógica futura de validación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_billing_details');
    }
};
