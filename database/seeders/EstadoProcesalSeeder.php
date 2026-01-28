<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoProcesal;

class EstadoProcesalSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Inicial', 'descripcion' => 'Inicio del asunto.'],
            ['nombre' => 'Trámite', 'descripcion' => 'El asunto se encuentra en trámite.'],
            ['nombre' => 'Sentencia', 'descripcion' => 'El asunto se encuentra en etapa de sentencia.'],
            ['nombre' => 'Ejecución', 'descripcion' => 'El asunto se encuentra en etapa de ejecución.'],
            ['nombre' => 'Suspendido', 'descripcion' => 'El asunto se encuentra suspendido.'],
            ['nombre' => 'Cerrado', 'descripcion' => 'El asunto se encuentra cerrado.'],
        ];

        foreach ($estados as $estado) {
            EstadoProcesal::firstOrCreate(
                ['nombre' => $estado['nombre']],
                ['descripcion' => $estado['descripcion']]
            );
        }
    }
}
