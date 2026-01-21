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
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->setAccessType('offline'); // Important for refresh tokens
        $this->client->setPrompt('consent'); // Force consent to get refresh token
        $this->client->addScope(Calendar::CALENDAR);
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
        if (!$this->setupClientForUser($user)) {
            return false;
        }

        try {
            $service = new Calendar($this->client);

            $event = new Event([
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
            ]);

            $calendarId = 'primary';
            $event = $service->events->insert($calendarId, $event);

            return $event->id;

        } catch (\Exception $e) {
            Log::error('Google Calendar Create Event Error: ' . $e->getMessage());
            
            // If error is invalid_grant, token might be revoked
            if (str_contains($e->getMessage(), 'invalid_grant')) {
                // Clear tokens so user knows they need to reconnect
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
