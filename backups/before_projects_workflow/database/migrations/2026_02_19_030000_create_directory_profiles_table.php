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
        Schema::create('directory_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Public SEO data
            $table->string('slug')->unique()->nullable(); // e.g. 'juan-perez-abogado-penalista'
            $table->string('headline')->nullable(); // e.g. "Experto en Litigio Civil"
            $table->text('bio')->nullable(); // Markdown support
            $table->json('specialties')->nullable(); // ["Penal", "Civil"]
            
            // Location & Verification
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('professional_license')->nullable(); // Cédula
            $table->boolean('is_verified')->default(false); // Admin approval
            
            // Public Contact Info
            $table->string('whatsapp')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            
            // Visibility Control
            $table->boolean('is_public')->default(false);
            
            // Analytics
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('contact_clicks_count')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directory_profiles');
    }
};
