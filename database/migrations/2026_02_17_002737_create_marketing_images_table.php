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
        Schema::create('marketing_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            $table->text('prompt'); // La instrucción creativa
            $table->text('revised_prompt')->nullable(); // El prompt mejorado por la IA
            $table->string('style')->default('natural'); // vivid, natural, 3d, etc.
            $table->string('size')->default('1024x1024');
            $table->string('file_path'); // Ruta local en storage
            
            // Metadatos de IA
            $table->string('provider')->default('openai'); // dall-e-3
            $table->decimal('cost', 10, 4)->default(0); 
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_images');
    }
};
