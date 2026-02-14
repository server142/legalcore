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
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'OpenAI', 'Anthropic', 'Groq', etc.
            $table->string('slug')->unique(); // 'openai', 'anthropic', 'groq'
            $table->text('api_key')->nullable(); // Encrypted API Key
            $table->string('default_model')->nullable(); // 'gpt-4o-mini', 'claude-3-5-sonnet'
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // Para ordenar en la UI
            $table->timestamps();
        });

        // Add new setting to global_settings for active provider
        DB::table('global_settings')->insert([
            'key' => 'active_ai_provider_id',
            'value' => null,
            'description' => 'ID del proveedor de IA activo actualmente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
        DB::table('global_settings')->where('key', 'active_ai_provider_id')->delete();
    }
};
