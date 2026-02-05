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
        Schema::create('dof_publications', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_publicacion')->index();
            $table->string('cod_nota')->unique()->comment('Code from DOF specific to the note');
            $table->text('titulo');
            $table->longText('resumen')->nullable();
            $table->longText('texto_completo')->nullable()->comment('Full text intended for semantic indexing');
            $table->string('link_pdf')->nullable();
            $table->string('seccion')->nullable();
            $table->string('organismo')->nullable();
            $table->json('embedding_data')->nullable()->comment('Placeholder for vector embeddings');
            $table->timestamps();
            
            // Optional: Fulltext index for traditional search if MySQL/MariaDB
            // $table->fullText(['titulo', 'resumen', 'texto_completo']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dof_publications');
    }
};
