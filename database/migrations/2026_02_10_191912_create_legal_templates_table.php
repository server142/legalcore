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
        Schema::create('legal_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade')->comment('Null means global template');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->index()->comment('e.g., Contract, Demand, Agreement');
            $table->string('materia')->index()->comment('e.g., Family, Civil, Penal');
            $table->string('file_path');
            $table->string('extension', 10);
            $table->boolean('is_global')->default(false);
            $table->longText('extracted_text')->nullable();
            $table->json('placeholders')->nullable()->comment('Detected variables via AI');
            $table->json('embedding_data')->nullable();
            $table->unsignedInteger('downloads_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->fullText(['name', 'description', 'extracted_text']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_templates');
    }
};
