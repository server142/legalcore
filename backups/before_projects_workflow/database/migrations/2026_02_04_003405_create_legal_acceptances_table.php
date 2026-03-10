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
        Schema::create('legal_acceptances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('legal_document_id')->index();
            $table->string('version');
            $table->timestamp('fecha_aceptacion')->useCurrent();
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('legal_document_id')->references('id')->on('legal_documents')->onDelete('cascade');
            
            // Un usuario solo acepta una versión específica de un documento una vez
            $table->unique(['user_id', 'legal_document_id', 'version'], 'user_doc_version_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_acceptances');
    }
};
