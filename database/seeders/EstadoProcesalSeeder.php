<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoProcesal;
use Illuminate\Support\Facades\DB;

class EstadoProcesalSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Radicación/Inicio', 'descripcion' => 'Radicado o ingresado el expediente en la Junta o Juzgado.'],
            ['nombre' => 'Notificación/Emplazamiento', 'descripcion' => 'Se informa a las partes sobre el juicio.'],
            ['nombre' => 'Contestación', 'descripcion' => 'La parte demandada responde a la demanda.'],
            ['nombre' => 'Instrucción/Pruebas', 'descripcion' => 'Etapa para ofrecer y desahogar pruebas.'],
            ['nombre' => 'Alegatos', 'descripcion' => 'Conclusión de pruebas, las partes presentan argumentos finales.'],
            ['nombre' => 'Cierre de Instrucción', 'descripcion' => 'Se cierra la fase de pruebas y se espera resolución.'],
            ['nombre' => 'Laudo/Sentencia', 'descripcion' => 'Emisión de la resolución que pone fin al juicio.'],
            ['nombre' => 'Ejecución', 'descripcion' => 'Cumplimiento forzoso de la sentencia.'],
            ['nombre' => 'Archivo', 'descripcion' => 'Expediente concluido y almacenado.'],
        ];

        $nombres = array_map(fn ($e) => $e['nombre'], $estados);

        foreach ($estados as $estado) {
            EstadoProcesal::updateOrCreate(
                ['nombre' => $estado['nombre']],
                ['descripcion' => $estado['descripcion']]
            );
        }

        EstadoProcesal::whereNotIn('nombre', $nombres)->delete();

        $default = EstadoProcesal::where('nombre', 'Radicación/Inicio')->first();
        if ($default) {
            DB::table('expedientes')
                ->whereNull('estado_procesal_id')
                ->update([
                    'estado_procesal_id' => $default->id,
                    'estado_procesal' => $default->nombre,
                ]);
        }
    }
}
