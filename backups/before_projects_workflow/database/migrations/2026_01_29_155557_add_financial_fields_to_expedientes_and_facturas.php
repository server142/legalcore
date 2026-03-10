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
            if (!Schema::hasColumn('expedientes', 'honorarios_totales')) {
                $table->decimal('honorarios_totales', 15, 2)->default(0)->after('descripcion');
            }
            if (!Schema::hasColumn('expedientes', 'saldo_pendiente')) {
                $table->decimal('saldo_pendiente', 15, 2)->default(0)->after('honorarios_totales');
            }
        });

        Schema::table('facturas', function (Blueprint $table) {
            if (!Schema::hasColumn('facturas', 'expediente_id')) {
                $table->foreignId('expediente_id')->nullable()->constrained()->onDelete('set null')->after('cliente_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->dropColumn(['honorarios_totales', 'saldo_pendiente']);
        });

        Schema::table('facturas', function (Blueprint $table) {
            $table->dropForeign(['expediente_id']);
            $table->dropColumn('expediente_id');
        });
    }
};
