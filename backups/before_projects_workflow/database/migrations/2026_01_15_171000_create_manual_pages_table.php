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
        Schema::create('manual_pages', function (Blueprint $title) {
            $title->id();
            $title->string('title');
            $title->string('slug')->unique();
            $title->text('content');
            $title->string('image_path')->nullable();
            $title->integer('order')->default(0);
            $title->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_pages');
    }
};
