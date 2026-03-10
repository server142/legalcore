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
        Schema::table('dof_publications', function (Blueprint $table) {
            // Change column to longText to avoid JSON size limits or "table full" issues with large vectors
            // We use raw statement to be safe with cross-db or doctrine limitations
            DB::statement("ALTER TABLE dof_publications MODIFY embedding_data LONGTEXT NULL COMMENT 'Vector embeddings stored as JSON string'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dof_publications', function (Blueprint $table) {
            // Revert to json
             DB::statement("ALTER TABLE dof_publications MODIFY embedding_data JSON NULL COMMENT 'Placeholder for vector embeddings'");
        });
    }
};
