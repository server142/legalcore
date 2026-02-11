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
        if (!Schema::hasTable('legal_templates')) {
            Schema::create('legal_templates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('category')->nullable(); // Contratos, Familiar, Penal, etc.
                $table->string('materia')->nullable();
                $table->string('file_path');
                $table->string('extension', 10); // docx, pdf, txt
                $table->longText('extracted_text')->nullable();
                $table->json('placeholders')->nullable(); // Detected keys like [NOMBRE_CLIENTE]
                $table->json('embedding')->nullable(); // Changed from vector to json for compatibility
                $table->boolean('is_global')->default(false); // If true, visible to everyone
                $table->unsignedInteger('download_count')->default(0);
                $table->softDeletes();
                $table->timestamps();
            });

            // Index for fast search
            DB::statement('ALTER TABLE legal_templates ADD FULLTEXT fulltext_index (name, description, extracted_text)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_templates');
    }
};
