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
        Schema::create('expediente_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('expediente_id')->constrained()->onDelete('cascade');
            $table->foreignId('factura_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('monto', 10, 2);
            $table->enum('tipo_pago', ['anticipo', 'parcial', 'liquidacion'])->default('anticipo');
            $table->string('metodo_pago');
            $table->string('referencia')->nullable();
            $table->date('fecha_pago');
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'expediente_id']);
            $table->index(['tenant_id', 'fecha_pago']);
            $table->index(['expediente_id', 'tipo_pago']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expediente_pagos');
    }
};
