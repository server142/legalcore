<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dof_publications', function (Blueprint $table) {
            // Add fulltext index to titulo for fast searches
            // Note: If using MySQL < 5.6 or MariaDB < 10.0.5, this might not support InnoDB.
            // But modern versions do.
            $table->fullText('titulo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dof_publications', function (Blueprint $table) {
            $table->dropFullText(['titulo']);
        });
    }
};
