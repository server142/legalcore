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
        Schema::create('sjf_publications', function (Blueprint $table) {
            $table->id();
            $table->string('reg_digital')->unique()->comment('Registro digital de la tesis');
            $table->text('rubro')->nullable();
            $table->longText('texto')->nullable();
            $table->longText('precedentes')->nullable();
            
            $table->string('localizacion')->nullable()->comment('Epoca, instancia, fuente, etc');
            $table->date('fecha_publicacion')->nullable();
            
            $table->string('tipo_tesis')->nullable()->comment('Jurisprudencia o Tesis Aislada');
            $table->string('instancia')->nullable(); // Primera Sala, Plenos, etc.
            $table->string('materia')->nullable();   // Común, Penal, Civil, etc.
            
            // For Semantic Search
            $table->json('embedding_data')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sjf_publications');
    }
};
