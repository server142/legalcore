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
        
        // Verificar si tenemos Service Account configurada
        $hasServiceAccount = config('services.google.service_account_json') && file_exists(config('services.google.service_account_json'));

        // Sincronizar si: (Hay Service Account Y usuario tiene email válido) O (Usuario tiene token OAuth)
        // Prioridad: calendar_email > email
        $targetEmail = $user->calendar_email ?? $user->email;

        if (($hasServiceAccount && $targetEmail) || ($user && $user->google_access_token)) {
            try {
                // Si usamos Service Account, pasamos el email objetivo explícitamente
                $eventData = [
                    'title' => $evento->titulo,
                    'description' => $evento->descripcion,
                    'start' => $evento->start_time,
                    'end' => $evento->end_time,
                ];

                if ($hasServiceAccount) {
                    $eventData['attendee_email'] = $targetEmail;
                }

                $googleEventId = $this->googleService->createEvent($user, $eventData);

                if ($googleEventId) {
                    // Opcional: Guardar el ID del evento de Google en la base de datos si quisieras actualizarlo después
                    // $evento->google_event_id = $googleEventId;
                    // $evento->saveQuietly();
                    Log::info("Evento sincronizado con Google Calendar: {$evento->id} -> {$googleEventId}");
                }
            } catch (\Exception $e) {
                Log::error("Error sincronizando evento {$evento->id} con Google: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Evento "updated" event.
     */
    public function updated(Evento $evento): void
    {
        // Aquí podrías implementar la lógica para actualizar el evento en Google
    }

    /**
     * Handle the Evento "deleted" event.
     */
    public function deleted(Evento $evento): void
    {
        // Aquí podrías implementar la lógica para eliminar el evento en Google
    }
}
