<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixEstadosOrderSeeder extends Seeder
{
    public function run(): void
    {
        // Define logical order
        $order = [
            'RADICACIÓN/INICIO' => 10,
            'NOTIFICACIÓN/EMPLAZAMIENTO' => 20,
            'CONTESTACIÓN' => 30,
            'INSTRUCCIÓN/PRUEBAS' => 40,
            'ALEGATOS' => 50,
            'CIERRE DE INSTRUCCIÓN' => 60,
            'LAUDO/SENTENCIA' => 70,
            'EJECUCIÓN' => 80,
            'ARCHIVO' => 90
        ];

        foreach ($order as $nombre => $orden) {
            DB::table('estados_procesales')
                ->where('nombre', $nombre)
                ->update(['orden' => $orden]);
        }
        
        $this->command->info('Estados procesales reordenados correctamente.');
    }
}
