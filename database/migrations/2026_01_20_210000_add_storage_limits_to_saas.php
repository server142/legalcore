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
        // Añadir límite de almacenamiento a los planes
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (!Schema::hasColumn('plans', 'storage_limit_gb')) {
                    $table->integer('storage_limit_gb')->default(1)->after('max_lawyer_users'); // 1GB por defecto
                }
            });
        }

        // Añadir tamaño a los documentos para rastrear uso
        if (Schema::hasTable('documentos')) {
            Schema::table('documentos', function (Blueprint $table) {
                if (!Schema::hasColumn('documentos', 'size')) {
                    $table->bigInteger('size')->default(0)->after('path'); // Tamaño en bytes
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('storage_limit_gb');
        });
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
};
