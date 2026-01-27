<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Evento;
use App\Models\User;
use App\Models\Expediente;
use Carbon\Carbon;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $this->command->error('No users found. Please run TenantSeeder first.');
            return;
        }

        $expediente = Expediente::where('tenant_id', $user->tenant_id)->first();

        // 1. Evento de Audiencia (Próximo)
        Evento::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'expediente_id' => $expediente ? $expediente->id : null,
            'titulo' => 'Audiencia de Pruebas y Alegatos',
            'descripcion' => 'Presentación de testigos y pruebas documentales en el juzgado.',
            'start_time' => Carbon::now()->addDays(2)->setTime(10, 0),
            'end_time' => Carbon::now()->addDays(2)->setTime(12, 0),
            'tipo' => 'audiencia',
        ]);

        // 2. Término Legal (Mañana)
        Evento::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'expediente_id' => $expediente ? $expediente->id : null,
            'titulo' => 'Vencimiento de Término: Contestación',
            'descripcion' => 'Último día para presentar la contestación de la demanda.',
            'start_time' => Carbon::now()->addDay()->setTime(23, 59),
            'end_time' => Carbon::now()->addDay()->setTime(23, 59),
            'tipo' => 'termino',
        ]);

        // 3. Cita Personal (Hoy)
        Evento::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'titulo' => 'Reunión con Cliente Nuevo',
            'descripcion' => 'Entrevista inicial para revisión de contrato.',
            'start_time' => Carbon::now()->addHours(2),
            'end_time' => Carbon::now()->addHours(3),
            'tipo' => 'cita',
        ]);

        // 4. Evento pasado (para ver historial)
        Evento::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'titulo' => 'Revisión de Expediente 123/2023',
            'descripcion' => 'Se revisó el estado procesal en el boletín judicial.',
            'start_time' => Carbon::now()->subDays(5)->setTime(9, 0),
            'end_time' => Carbon::now()->subDays(5)->setTime(10, 0),
            'tipo' => 'cita',
        ]);

        $this->command->info('AgendaSeeder: Eventos de prueba creados exitosamente.');
    }
}
