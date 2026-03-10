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
            // Add fulltext index to titulo and resumen for fast searches
            $table->fullText(['titulo', 'resumen']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dof_publications', function (Blueprint $table) {
            $table->dropFullText(['titulo', 'resumen']);
        });
    }
};
