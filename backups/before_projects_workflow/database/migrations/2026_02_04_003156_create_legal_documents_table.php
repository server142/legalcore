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
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->string('nombre');
            $table->enum('tipo', ['PRIVACIDAD', 'TERMINOS', 'COOKIES', 'CONTRATO_SAAS', 'OTRO'])->default('OTRO');
            $table->longText('texto');
            $table->string('version')->default('1.0');
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_publicacion')->nullable();
            $table->boolean('requiere_aceptacion')->default(true);
            $table->json('visible_en')->nullable(); // ['registro', 'login', 'onboarding', 'footer']
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
    }
};
