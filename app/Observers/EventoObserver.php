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

        $hasServiceAccount = config('services.google.service_account_json') && file_exists(config('services.google.service_account_json'));

        if ($hasServiceAccount || ($user && $user->google_access_token)) {
            try {
                $eventData = [
                    'title' => $evento->titulo,
                    'description' => $evento->descripcion,
                    'start' => $evento->start_time,
                    'end' => $evento->end_time,
                ];

                // Si hay expediente, invitar a todos los asignados
                if ($evento->expediente_id && $evento->expediente) {
                    $emails = [];
                    
                    // Responsable
                    if ($evento->expediente->abogado) {
                        $emails[] = $evento->expediente->abogado->calendar_email ?? $evento->expediente->abogado->email;
                    }
                    
                    // Asignados
                    if ($evento->expediente->assignedUsers) {
                        foreach ($evento->expediente->assignedUsers as $assignedUser) {
                            $email = $assignedUser->calendar_email ?? $assignedUser->email;
                            if ($email) {
                                $emails[] = $email;
                            }
                        }
                    }
                    
                    $eventData['attendees'] = array_unique(array_filter($emails));
                } else {
                    $eventData['attendee_email'] = $user->calendar_email ?? $user->email;
                }

                $googleEventId = $this->googleService->createEvent($user, $eventData);

                if ($googleEventId) {
                    $evento->google_event_id = $googleEventId;
                    $evento->saveQuietly();
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
        if (!$evento->google_event_id) {
            $this->created($evento);
            return;
        }

        $user = $evento->user;
        if (!$user) {
            return;
        }

        try {
            $eventData = [
                'title' => $evento->titulo,
                'description' => $evento->descripcion,
                'start' => $evento->start_time,
                'end' => $evento->end_time,
            ];

            if ($evento->expediente_id && $evento->expediente) {
                $emails = [];
                
                if ($evento->expediente->abogado) {
                    $emails[] = $evento->expediente->abogado->calendar_email ?? $evento->expediente->abogado->email;
                }
                
                if ($evento->expediente->assignedUsers) {
                    foreach ($evento->expediente->assignedUsers as $assignedUser) {
                        $email = $assignedUser->calendar_email ?? $assignedUser->email;
                        if ($email) {
                            $emails[] = $email;
                        }
                    }
                }
                
                $eventData['attendees'] = array_unique(array_filter($emails));
            } else {
                $eventData['attendee_email'] = $user->calendar_email ?? $user->email;
            }

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
