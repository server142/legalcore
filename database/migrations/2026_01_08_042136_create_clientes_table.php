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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->enum('tipo', ['persona_fisica', 'persona_moral'])->default('persona_fisica');
            $table->string('rfc')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->text('datos_fiscales')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
