<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    protected $googleService;

    public function __construct(GoogleCalendarService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function redirectToGoogle()
    {
        return redirect($this->googleService->getAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('code')) {
            $success = $this->googleService->authenticate($request->get('code'), Auth::user());

            if ($success) {
                return redirect()->route('profile')->with('status', 'google-connected');
            }
        }

        return redirect()->route('profile')->with('error', 'No se pudo conectar con Google Calendar.');
    }
    
    public function disconnect()
    {
        $user = Auth::user();
        $user->update([
            'google_access_token' => null,
            'google_refresh_token' => null,
            'google_token_expires_at' => null,
            'google_calendar_id' => null,
        ]);
        
        return redirect()->route('profile')->with('status', 'google-disconnected');
    }
}
