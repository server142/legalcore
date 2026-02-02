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
        $hasServiceAccount = config('services.google.service_account_json') && file_exists(config('services.google.service_account_json'));
        
        // Bloqueo original eliminado: Ahora permitimos si hay Cuenta de Servicio o Token de Usuario
        if (!$hasServiceAccount && (!$user || !$user->google_access_token)) {
            Log::info("Sincronización de Google Calendar omitida: No hay Cuenta de Servicio ni Token de Usuario.");
            return;
        }

        $attendees = $this->getAttendeesEmails($evento);

        // Si usamos Cuenta de Servicio, el creador también debe ser asistente para que le aparezca
        if ($hasServiceAccount && $user && !in_array($user->calendar_email ?? $user->email, $attendees)) {
            $attendees[] = $user->calendar_email ?? $user->email;
        }

        $eventData = [
            'title' => $evento->titulo,
            'description' => $evento->descripcion,
            'start' => $evento->start_time,
            'end' => $evento->end_time,
            'attendees' => $attendees,
        ];

        try {
            // El servicio ya maneja internamente si usa Cuenta de Servicio o Token de Usuario
            $eventId = $this->googleService->createEvent($user, $eventData);
            
            if ($eventId) {
                $evento->google_event_id = $eventId;
                $evento->saveQuietly();
                Log::info("Evento sincronizado con Google Calendar: {$eventId} para el usuario {$user->email}");
            }
        } catch (\Exception $e) {
            Log::error("Error sincronizando evento Google para {$user->email}: " . $e->getMessage());
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
        $hasServiceAccount = config('services.google.service_account_json') && file_exists(config('services.google.service_account_json'));

        if (!$hasServiceAccount && (!$user || !$user->google_access_token)) {
            return;
        }

        $attendees = $this->getAttendeesEmails($evento);

        // Si usamos Cuenta de Servicio, asegurar que el creador esté invitado
        if ($hasServiceAccount && $user && !in_array($user->calendar_email ?? $user->email, $attendees)) {
            $attendees[] = $user->calendar_email ?? $user->email;
        }

        $eventData = [
            'title' => $evento->titulo,
            'description' => $evento->descripcion,
            'start' => $evento->start_time,
            'end' => $evento->end_time,
            'attendees' => $attendees,
        ];

        try {
            $this->googleService->updateEvent($user, $evento->google_event_id, $eventData);
            Log::info("Evento actualizado en Google Calendar: {$evento->google_event_id}");
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
            ->map(function ($u) {
                return $u->calendar_email ?? $u->email;
            })
            ->filter()
            ->toArray();
    }

    /**
     * Handle the Evento "deleted" event.
     */
    public function deleted(Evento $evento): void
    {
        if ($evento->google_event_id) {
            $user = $evento->user;
            $hasServiceAccount = config('services.google.service_account_json') && file_exists(config('services.google.service_account_json'));

            if (!$hasServiceAccount && (!$user || !$user->google_access_token)) {
                return;
            }

            try {
                $this->googleService->deleteEvent($user, $evento->google_event_id);
                Log::info("Evento eliminado de Google Calendar: {$evento->google_event_id}");
            } catch (\Exception $e) {
                Log::error("Error eliminando evento {$evento->id} de Google: " . $e->getMessage());
            }
        }
    }
}
