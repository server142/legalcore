<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        
        // Opción A: Cuenta de Servicio (Prioridad)
        if (config('services.google.service_account_json') && file_exists(config('services.google.service_account_json'))) {
            $this->client->setAuthConfig(config('services.google.service_account_json'));
            $this->client->addScope(Calendar::CALENDAR);
        } 
        // Opción B: OAuth (Respaldo / Legacy)
        else {
            $this->client->setClientId(config('services.google.client_id'));
            $this->client->setClientSecret(config('services.google.client_secret'));
            $this->client->setRedirectUri(config('services.google.redirect'));
            $this->client->setAccessType('offline');
            $this->client->setPrompt('consent');
            $this->client->addScope(Calendar::CALENDAR);
        }
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function authenticate($code, User $user)
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                throw new \Exception('Error fetching access token: ' . $token['error']);
            }

            $this->storeToken($user, $token);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar Auth Error: ' . $e->getMessage());
            return false;
        }
    }

    protected function storeToken(User $user, array $token)
    {
        $user->google_access_token = $token['access_token'];
        
        if (isset($token['refresh_token'])) {
            $user->google_refresh_token = $token['refresh_token'];
        }

        if (isset($token['expires_in'])) {
            $user->google_token_expires_at = Carbon::now()->addSeconds($token['expires_in']);
        }

        $user->save();
    }

    public function createEvent(User $user, $eventData)
    {
        $usingServiceAccount = config('services.google.service_account_json') && file_exists(config('services.google.service_account_json'));

        if (!$usingServiceAccount) {
            if (!$this->setupClientForUser($user)) {
                return false;
            }
        }

        try {
            $service = new Calendar($this->client);

            $eventParams = [
                'summary' => $eventData['title'],
                'description' => $eventData['description'] ?? '',
                'start' => [
                    'dateTime' => Carbon::parse($eventData['start'])->toRfc3339String(),
                    'timeZone' => config('app.timezone'),
                ],
                'end' => [
                    'dateTime' => Carbon::parse($eventData['end'])->toRfc3339String(),
                    'timeZone' => config('app.timezone'),
                ],
            ];

            // Manejo de asistentes (attendees)
            $attendees = [];
            $targetCalendarId = 'primary';

            if ($usingServiceAccount) {
                // Si usamos cuenta de servicio, el calendarId debe ser el correo del usuario
                // y el usuario debe haber compartido su calendario con el email del robot.
                $targetCalendarId = $user->calendar_email ?? $user->email;
            }

            if (isset($eventData['attendees']) && is_array($eventData['attendees'])) {
                foreach ($eventData['attendees'] as $email) {
                    // No invitar al dueño del calendario si ya estamos insertando en su calendario
                    if ($usingServiceAccount && $email === $targetCalendarId) {
                        continue;
                    }
                    $attendees[] = ['email' => $email];
                }
            } else {
                $attendeeEmail = $eventData['attendee_email'] ?? $user->email;
                if ($attendeeEmail && (!$usingServiceAccount || $attendeeEmail !== $targetCalendarId)) {
                    $attendees[] = ['email' => $attendeeEmail];
                }
            }

            if (!empty($attendees)) {
                $eventParams['attendees'] = $attendees;
            }

            $event = new Event($eventParams);
            $optParams = ['sendUpdates' => 'all']; 
            
            Log::info('Google Calendar: Intentando insertar evento', [
                'calendarId' => $targetCalendarId,
                'attendees' => $attendees,
                'usingServiceAccount' => $usingServiceAccount
            ]);

            $event = $service->events->insert($targetCalendarId, $event, $optParams);

            Log::info('Google Calendar: Evento insertado exitosamente', ['id' => $event->id]);

            return $event->id;

        } catch (\Exception $e) {
            Log::error('Google Calendar Create Event Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function updateEvent(User $user, $googleEventId, $eventData)
    {
        $usingServiceAccount = config('services.google.service_account_json') && file_exists(config('services.google.service_account_json'));

        if (!$usingServiceAccount) {
            if (!$this->setupClientForUser($user)) {
                return false;
            }
        }

        try {
            $service = new Calendar($this->client);
            $targetCalendarId = $usingServiceAccount ? ($user->calendar_email ?? $user->email) : 'primary';
            
            $event = $service->events->get($targetCalendarId, $googleEventId);

            $event->setSummary($eventData['title']);
            $event->setDescription($eventData['description'] ?? '');
            
            $start = new \Google\Service\Calendar\EventDateTime();
            $start->setDateTime(Carbon::parse($eventData['start'])->toRfc3339String());
            $start->setTimeZone(config('app.timezone'));
            $event->setStart($start);

            $end = new \Google\Service\Calendar\EventDateTime();
            $end->setDateTime(Carbon::parse($eventData['end'])->toRfc3339String());
            $end->setTimeZone(config('app.timezone'));
            $event->setEnd($end);

            // Actualizar asistentes si se proporcionan
            if (isset($eventData['attendees']) && is_array($eventData['attendees'])) {
                $attendees = [];
                foreach ($eventData['attendees'] as $email) {
                    if ($usingServiceAccount && $email === $targetCalendarId) {
                        continue;
                    }
                    $attendees[] = new \Google\Service\Calendar\EventAttendee(['email' => $email]);
                }
                $event->setAttendees($attendees);
            }

            $updatedEvent = $service->events->update($targetCalendarId, $googleEventId, $event, ['sendUpdates' => 'all']);
            return $updatedEvent->id;

        } catch (\Exception $e) {
            Log::error('Google Calendar Update Event Error: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteEvent(User $user, $googleEventId)
    {
        $usingServiceAccount = config('services.google.service_account_json') && file_exists(config('services.google.service_account_json'));

        if (!$usingServiceAccount) {
            if (!$this->setupClientForUser($user)) {
                return false;
            }
        }

        try {
            $service = new Calendar($this->client);
            $targetCalendarId = $usingServiceAccount ? ($user->calendar_email ?? $user->email) : 'primary';
            $service->events->delete($targetCalendarId, $googleEventId, ['sendUpdates' => 'all']);
            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar Delete Event Error: ' . $e->getMessage());
            return false;
        }
    }

    protected function setupClientForUser(User $user)
    {
        if (!$user->google_access_token) {
            return false;
        }

        $this->client->setAccessToken($user->google_access_token);

        if ($this->client->isAccessTokenExpired()) {
            if ($user->google_refresh_token) {
                try {
                    $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                    $this->storeToken($user, $newToken);
                } catch (\Exception $e) {
                    Log::error('Error refreshing token: ' . $e->getMessage());
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }
}
