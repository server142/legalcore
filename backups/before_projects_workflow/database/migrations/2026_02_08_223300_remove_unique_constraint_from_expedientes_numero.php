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
        Schema::table('expedientes', function (Blueprint $table) {
            // Drop the simple unique constraint on 'numero'
            // The exact name is usually 'expedientes_numero_unique' but using array syntax is safer
            $table->dropUnique(['numero']);
            
            // Allow duplicate numbers in the database layer.
            // Application validation handles the logic (unique per Juzgado + Tenant).
            // We could add a composite unique key, but since 'juzgado' is a string and nullable, it's tricky.
            // For flexibility, we rely on application logic + regular index.
            
            $table->index(['numero', 'tenant_id']); // Add regular index for performance
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            // Re-add the unique constraint if rolling back (might fail if data already has duplicates)
            $table->unique('numero');
            $table->dropIndex(['numero', 'tenant_id']);
        });
    }
};
