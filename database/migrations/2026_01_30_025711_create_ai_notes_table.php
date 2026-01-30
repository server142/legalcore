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
        Schema::create('ai_notes', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index(); // Multi-tenancy
            $table->foreignId('expediente_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Who saved it
            $table->longText('content'); // The AI response
            $table->timestamps();
            $table->softDeletes();
            
            // Optional: Index for faster retrieval by expediente
            $table->index(['expediente_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_notes');
    }
};
