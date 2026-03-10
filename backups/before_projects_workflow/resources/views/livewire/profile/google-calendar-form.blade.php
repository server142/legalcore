<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $isConnected = false;
    public $email = '';

    public function mount(): void
    {
        $this->isConnected = !empty(Auth::user()->google_access_token);
        // We might want to store the connected email if we had it, but for now just status
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Google Calendar') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Connect your account to sync events automatically with Google Calendar.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        @if (session('status') === 'google-connected')
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                <span class="font-medium">¡Conectado!</span> Tu calendario de Google está sincronizado.
            </div>
        @endif

        @if (session('status') === 'google-disconnected')
            <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                <span class="font-medium">Desconectado.</span> Se ha eliminado la conexión con Google Calendar.
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                <span class="font-medium">Error:</span> {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center gap-4">
            @if ($isConnected)
                <div class="flex items-center text-green-600">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ __('Connected') }}</span>
                </div>

                <form method="POST" action="{{ route('auth.google.disconnect') }}">
                    @csrf
                    <x-danger-button>
                        {{ __('Disconnect') }}
                    </x-danger-button>
                </form>
            @else
                <a href="{{ route('auth.google') }}">
                    <x-secondary-button type="button" class="flex items-center">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                        </svg>
                        {{ __('Connect with Google Calendar') }}
                    </x-secondary-button>
                </a>
            @endif
        </div>
    </div>
</section>
