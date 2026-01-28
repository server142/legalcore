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
        Schema::table('expedientes', function (Blueprint $table) {
            $table->foreignId('estado_procesal_id')
                ->nullable()
                ->after('estado_procesal')
                ->constrained('estados_procesales')
                ->nullOnDelete();
        });

        $estados = DB::table('estados_procesales')->get(['id', 'nombre']);
        foreach ($estados as $estado) {
            DB::table('expedientes')
                ->whereNull('estado_procesal_id')
                ->where(function ($q) use ($estado) {
                    $q->where('estado_procesal', $estado->nombre)
                      ->orWhere('estado_procesal', strtolower($estado->nombre));
                })
                ->update(['estado_procesal_id' => $estado->id]);
        }

        $inicial = DB::table('estados_procesales')->where('nombre', 'Inicial')->first();
        if ($inicial) {
            DB::table('expedientes')
                ->whereNull('estado_procesal_id')
                ->where(function ($q) {
                    $q->whereNull('estado_procesal')
                      ->orWhere('estado_procesal', 'inicial')
                      ->orWhere('estado_procesal', 'Inicial');
                })
                ->update(['estado_procesal_id' => $inicial->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expedientes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('estado_procesal_id');
        });
    }
};
