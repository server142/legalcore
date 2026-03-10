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
        Schema::table('eventos', function (Blueprint $table) {
            if (!Schema::hasColumn('eventos', 'asesoria_id')) {
                $table->foreignId('asesoria_id')->nullable()->after('expediente_id')->constrained('asesorias')->nullOnDelete();
                $table->index(['tenant_id', 'user_id', 'start_time']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            if (Schema::hasColumn('eventos', 'asesoria_id')) {
                $table->dropForeign(['asesoria_id']);
                $table->dropColumn('asesoria_id');
            }
        });
    }
};
