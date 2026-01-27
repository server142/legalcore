<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comentario_reacciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comentario_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tipo'); // like, love, celebrate, support, insightful
            $table->timestamps();
            
            $table->unique(['comentario_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentario_reacciones');
    }
};
