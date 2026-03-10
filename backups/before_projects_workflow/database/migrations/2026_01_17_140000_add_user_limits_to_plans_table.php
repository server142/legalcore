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
        Schema::table('plans', function (Blueprint $table) {
            // Agregar límites de usuarios por plan
            $table->integer('max_admin_users')->default(1)->after('features'); // Cantidad de admins permitidos
            $table->integer('max_lawyer_users')->nullable()->after('max_admin_users'); // Cantidad de abogados permitidos (null = ilimitado)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['max_admin_users', 'max_lawyer_users']);
        });
    }
};
