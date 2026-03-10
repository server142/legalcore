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
        Schema::create('legal_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('legal_workflow_id')->constrained()->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('expediente_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('status')->default('active'); // active, paused, abandoned, converted
            $table->integer('current_step')->default(1);
            $table->json('data')->nullable(); // Respuestas recolectadas
            $table->integer('progress')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_projects');
    }
};
