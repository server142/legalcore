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
        // Determinar modo de operación
        $usingServiceAccount = config('services.google.service_account_json') && file_exists(config('services.google.service_account_json'));

        if (!$usingServiceAccount) {
            // Modo Legacy: OAuth Personal
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

            // Si usamos Service Account, AGREGAMOS al usuario como invitado
            // Usamos el email específico si viene en los datos, si no, el del usuario
            $attendeeEmail = $eventData['attendee_email'] ?? $user->email;
            
            if ($usingServiceAccount && $attendeeEmail) {
                $eventParams['attendees'] = [
                    ['email' => $attendeeEmail]
                ];
            }

            $event = new Event($eventParams);

            $calendarId = 'primary';
            $optParams = ['sendUpdates' => 'all']; // Forzar envío de correo de invitación
            $event = $service->events->insert($calendarId, $event, $optParams);

            return $event->id;

        } catch (\Exception $e) {
            Log::error('Google Calendar Create Event Error: ' . $e->getMessage());
            
            // Solo limpiar tokens si estamos en modo OAuth y falla por permisos
            if (!$usingServiceAccount && str_contains($e->getMessage(), 'invalid_grant')) {
                $user->update([
                    'google_access_token' => null,
                    'google_refresh_token' => null,
                    'google_token_expires_at' => null,
                ]);
            }
            
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
