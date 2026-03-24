<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asesoria;
use App\Models\Evento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class BotAsesoriaController extends Controller
{
    /**
     * Devuelve los horarios disponibles para una fecha específica.
     */
    public function getAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'duration_minutes' => 'nullable|integer|min:15|max:120',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $date = Carbon::parse($request->date);
        $durationMinutes = $request->duration_minutes ?? 30;
        $user = $request->user();
        $tenant = $user->tenant;
        $settings = $tenant?->settings ?? [];

        $workStart = $settings['asesorias_working_hours_start'] ?? '09:00';
        $workEnd = $settings['asesorias_working_hours_end'] ?? '18:00';
        $businessDays = $settings['asesorias_business_days'] ?? ['mon', 'tue', 'wed', 'thu', 'fri'];

        $dayKey = strtolower($date->format('D'));
        if (!in_array(substr($dayKey, 0, 3), $businessDays, true)) {
            return response()->json([
                'available' => false,
                'message' => 'El día seleccionado no es un día laboral.',
                'slots' => []
            ]);
        }

        $startLimit = (clone $date)->setTimeFromTimeString($workStart);
        $endLimit = (clone $date)->setTimeFromTimeString($workEnd);

        // Obtener eventos conflictivos de la agenda y de las asesorías
        $eventos = Evento::where('tenant_id', $user->tenant_id)
            ->where('user_id', $user->id)
            ->whereDate('start_time', $date)
            ->get();

        $asesorias = Asesoria::where('tenant_id', $user->tenant_id)
            ->where('abogado_id', $user->id)
            ->whereDate('fecha_hora', $date)
            ->whereIn('estado', ['agendada'])
            ->get();

        $slots = [];
        $candidate = clone $startLimit;

        while ($candidate->lt($endLimit)) {
            $candidateEnd = (clone $candidate)->addMinutes($durationMinutes);

            if ($candidateEnd->gt($endLimit)) {
                break;
            }

            // Validar si el slot candidato no cruza con un evento
            $conflict = false;
            foreach ($eventos as $ev) {
                if ($candidate->lt($ev->end_time) && $candidateEnd->gt($ev->start_time)) {
                    $conflict = true;
                    break;
                }
            }

            foreach ($asesorias as $as) {
                $asStart = clone $as->fecha_hora;
                $asEnd = (clone $asStart)->addMinutes($as->duracion_minutos ?? 30);
                if ($candidate->lt($asEnd) && $candidateEnd->gt($asStart)) {
                    $conflict = true;
                    break;
                }
            }

            // Validar que el candidato sea mayor a la hora actual
            if ($candidate->lt(now())) {
                $conflict = true;
            }

            if (!$conflict) {
                $slots[] = $candidate->format('H:i');
            }

            // Incremento para el siguiente slot
            $candidate->addMinutes(30); 
        }

        return response()->json([
            'available' => count($slots) > 0,
            'date' => $date->format('Y-m-d'),
            'slots' => $slots
        ]);
    }

    /**
     * Agenda una nueva asesoría.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_prospecto' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'asunto' => 'required|string|max:255',
            'fecha_hora' => 'required|date_format:Y-m-d H:i:s',
            'duracion_minutos' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = $request->user();
        $start = Carbon::parse($request->fecha_hora);
        $duration = $request->duracion_minutos ?? 30;
        $end = (clone $start)->addMinutes($duration);

        // Verificamos si no hay conflicto antes de agendar
        $conflictEvento = Evento::where('tenant_id', $user->tenant_id)
            ->where('user_id', $user->id)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();

        $conflictAsesoria = Asesoria::where('tenant_id', $user->tenant_id)
            ->where('abogado_id', $user->id)
            ->where('estado', 'agendada')
            ->where('fecha_hora', '<', $end)
            ->whereRaw('DATE_ADD(fecha_hora, INTERVAL COALESCE(duracion_minutos, 30) MINUTE) > ?', [$start])
            ->exists();

        if ($conflictEvento || $conflictAsesoria) {
            return response()->json([
                'error' => 'El horario seleccionado ya no está disponible.'
            ], 409);
        }

        $asesoria = Asesoria::create([
            'tenant_id' => $user->tenant_id,
            'abogado_id' => $user->id,
            'tipo' => 'videoconferencia', // Por defecto para landings
            'estado' => 'agendada',
            'nombre_prospecto' => $request->nombre_prospecto,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'asunto' => $request->asunto,
            'fecha_hora' => $start,
            'duracion_minutos' => $duration
        ]);
        
        // Crear el evento de agenda
        Evento::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'titulo' => 'Asesoría Inicial - ' . $request->nombre_prospecto,
            'descripcion' => "Asunto: " . $request->asunto . "\nTel: " . $request->telefono,
            'start_time' => $start,
            'end_time' => $end,
            'tipo' => 'cita'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asesoría agendada correctamente',
            'data' => $asesoria
        ], 201);
    }
}
