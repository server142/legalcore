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
        // Usamos raw SQL porque Doctrine DBAL a veces falla modificando ENUMS
        DB::statement("ALTER TABLE legal_documents MODIFY COLUMN tipo ENUM('PRIVACIDAD', 'TERMINOS', 'COOKIES', 'CONTRATO_SAAS', 'CONTRATO_SERVICIOS', 'OTRO') DEFAULT 'OTRO'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE legal_documents MODIFY COLUMN tipo ENUM('PRIVACIDAD', 'TERMINOS', 'COOKIES', 'CONTRATO_SAAS', 'OTRO') DEFAULT 'OTRO'");
    }
};
