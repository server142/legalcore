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
        // MySQL FullText indexes for fast keyword searching
        DB::statement('ALTER TABLE dof_publications ADD FULLTEXT fulltext_search (titulo, resumen)');
        
        // Ensure the table exists before adding index to SJF
        if (Schema::hasTable('sjf_publications')) {
            DB::statement('ALTER TABLE sjf_publications ADD FULLTEXT fulltext_search (rubro, texto)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dof_publications', function (Blueprint $table) {
            $table->dropIndex('fulltext_search');
        });

        if (Schema::hasTable('sjf_publications')) {
            Schema::table('sjf_publications', function (Blueprint $table) {
                $table->dropIndex('fulltext_search');
            });
        }
    }
};
