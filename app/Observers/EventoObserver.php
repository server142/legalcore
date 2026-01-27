<?php

namespace App\Observers;

use App\Models\Evento;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Log;

class EventoObserver
{
    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * Handle the Evento "created" event.
     */
    public function created(Evento $evento): void
    {
        $user = $evento->user;
        if (!$user) {
            return;
        }

        // Recolectar todos los usuarios que deben recibir el evento
        $targetUsers = collect([$user]); // Siempre incluir al creador

        if ($evento->expediente_id && $evento->expediente) {
            // Agregar responsable del expediente
            if ($evento->expediente->abogado) {
                $targetUsers->push($evento->expediente->abogado);
            }
            
            // Agregar usuarios asignados al expediente
            if ($evento->expediente->assignedUsers) {
                $targetUsers = $targetUsers->merge($evento->expediente->assignedUsers);
            }
        }

        // Agregar invitados manuales
        if ($evento->invitedUsers) {
            $targetUsers = $targetUsers->merge($evento->invitedUsers);
        }

        // Eliminar duplicados por ID
        $targetUsers = $targetUsers->unique('id');

        $eventData = [
            'title' => $evento->titulo,
            'description' => $evento->descripcion,
            'start' => $evento->start_time,
            'end' => $evento->end_time,
        ];

        $googleEventId = null;
        $syncedCount = 0;

        // Crear el evento en el calendario de cada usuario que tenga Google conectado
        foreach ($targetUsers as $targetUser) {
            if ($targetUser->google_access_token) {
                try {
                    $eventId = $this->googleService->createEvent($targetUser, $eventData);
                    
                    if ($eventId) {
                        // Guardar solo el primer ID exitoso como referencia
                        if (!$googleEventId) {
                            $googleEventId = $eventId;
                        }
                        $syncedCount++;
                        Log::info("Evento creado en calendario de {$targetUser->email}: {$eventId}");
                    }
                } catch (\Exception $e) {
                    Log::error("Error creando evento para {$targetUser->email}: " . $e->getMessage());
                }
            }
        }

        // Guardar el ID de referencia si al menos un calendario se sincronizó
        if ($googleEventId) {
            $evento->google_event_id = $googleEventId;
            $evento->saveQuietly();
            Log::info("Evento {$evento->id} sincronizado con {$syncedCount} calendarios");
        }
    }

    /**
     * Handle the Evento "updated" event.
     */
    public function updated(Evento $evento): void
    {
        if (!$evento->google_event_id) {
            $this->created($evento);
            return;
        }

        $user = $evento->user;
        if (!$user) {
            return;
        }

        // Recolectar todos los usuarios que deben tener el evento
        $targetUsers = collect([$user]);

        if ($evento->expediente_id && $evento->expediente) {
            if ($evento->expediente->abogado) {
                $targetUsers->push($evento->expediente->abogado);
            }
            
            if ($evento->expediente->assignedUsers) {
                $targetUsers = $targetUsers->merge($evento->expediente->assignedUsers);
            }
        }

        if ($evento->invitedUsers) {
            $targetUsers = $targetUsers->merge($evento->invitedUsers);
        }

        $targetUsers = $targetUsers->unique('id');

        $eventData = [
            'title' => $evento->titulo,
            'description' => $evento->descripcion,
            'start' => $evento->start_time,
            'end' => $evento->end_time,
        ];

        // Actualizar en cada calendario conectado
        // Nota: Como cada usuario tiene su propio evento con ID diferente,
        // esto solo funcionará si guardamos múltiples IDs. Por ahora, solo
        // actualizamos en el calendario del creador.
        try {
            $this->googleService->updateEvent($user, $evento->google_event_id, $eventData);
            Log::info("Evento actualizado en Google Calendar: {$evento->id}");
        } catch (\Exception $e) {
            Log::error("Error actualizando evento {$evento->id} en Google: " . $e->getMessage());
        }
    }

    /**
     * Handle the Evento "deleted" event.
     */
    public function deleted(Evento $evento): void
    {
        if ($evento->google_event_id) {
            try {
                $this->googleService->deleteEvent($evento->user, $evento->google_event_id);
                Log::info("Evento eliminado de Google Calendar: {$evento->id}");
            } catch (\Exception $e) {
                Log::error("Error eliminando evento {$evento->id} de Google: " . $e->getMessage());
            }
        }
    }
}
