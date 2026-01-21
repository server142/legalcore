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
        // Solo sincronizar si el usuario tiene token de Google
        $user = $evento->user;

        if ($user && $user->google_access_token) {
            try {
                $googleEventId = $this->googleService->createEvent($user, [
                    'title' => $evento->titulo,
                    'description' => $evento->descripcion,
                    'start' => $evento->start_time,
                    'end' => $evento->end_time,
                ]);

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
