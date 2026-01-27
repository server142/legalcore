<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comentario_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('comentario_id')->constrained()->onDelete('cascade');
            $table->timestamp('read_at')->useCurrent();
            $table->unique(['user_id', 'comentario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentario_reads');
    }
};
