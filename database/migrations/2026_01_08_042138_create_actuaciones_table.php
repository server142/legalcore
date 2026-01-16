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
        Schema::create('actuaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('expediente_id')->constrained()->onDelete('cascade');
            $table->date('fecha');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->boolean('es_plazo')->default(false);
            $table->string('estado')->default('pendiente'); // pendiente, completada, vencida
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actuaciones');
    }
};
