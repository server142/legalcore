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
        if (!$user || !$user->google_access_token) {
            return;
        }

        $attendees = $this->getAttendeesEmails($evento);

        $eventData = [
            'title' => $evento->titulo,
            'description' => $evento->descripcion,
            'start' => $evento->start_time,
            'end' => $evento->end_time,
            'attendees' => $attendees,
        ];

        try {
            $eventId = $this->googleService->createEvent($user, $eventData);
            
            if ($eventId) {
                $evento->google_event_id = $eventId;
                $evento->saveQuietly();
                Log::info("Evento creado en Google Calendar: {$eventId} con " . count($attendees) . " asistentes");
            }
        } catch (\Exception $e) {
            Log::error("Error creando evento para {$user->email}: " . $e->getMessage());
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
        if (!$user || !$user->google_access_token) {
            return;
        }

        $attendees = $this->getAttendeesEmails($evento);

        $eventData = [
            'title' => $evento->titulo,
            'description' => $evento->descripcion,
            'start' => $evento->start_time,
            'end' => $evento->end_time,
            'attendees' => $attendees,
        ];

        try {
            $this->googleService->updateEvent($user, $evento->google_event_id, $eventData);
            Log::info("Evento actualizado en Google Calendar: {$evento->id} con " . count($attendees) . " asistentes");
        } catch (\Exception $e) {
            Log::error("Error actualizando evento {$evento->id} en Google: " . $e->getMessage());
        }
    }

    protected function getAttendeesEmails(Evento $evento): array
    {
        $targetUsers = collect([]);

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

        return $targetUsers->unique('id')
            ->reject(function ($u) use ($evento) {
                return $u->id === $evento->user_id;
            })
            ->pluck('email')
            ->toArray();
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
