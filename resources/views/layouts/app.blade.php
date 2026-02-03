<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-100" x-data="{ mobileMenuOpen: false }">
        <div class="flex h-screen overflow-hidden bg-gray-100">
            <!-- Sidebar Component -->
            <livewire:layout.navigation />

            <!-- Main Content Area -->
            <div class="flex flex-col flex-1 min-w-0 overflow-hidden bg-gray-100">
                <!-- Top Header -->
                <header class="bg-white border-b border-gray-200 z-20 flex-shrink-0">
                    <div class="px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
                        <div class="flex items-center">
                            <!-- Mobile Toggle Button (Only visible on mobile) -->
                            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-gray-500 hover:text-gray-600 focus:outline-none p-2 rounded-md hover:bg-gray-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                            
                            @if (isset($header))
                                <div class="ml-4 lg:ml-0 font-semibold text-gray-800">
                                    {{ $header }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="flex items-center space-x-2">
                            <livewire:layout.messages-notification />
                            
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out px-3 py-2 rounded-md hover:bg-gray-50">
                                        <div class="mr-1">{{ auth()->user()->name }}</div>
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile')" wire:navigate>{{ __('Perfil') }}</x-dropdown-link>
                                    <livewire:layout.logout-button />
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <!-- Subscription Warning Banner -->
                @php
                    $tenant = auth()->user()->tenant;
                    $showWarning = false;
                    $warningMessage = '';
                    $warningType = 'warning'; // warning, danger

                    if ($tenant && !auth()->user()->hasRole('super_admin')) {
                        if ($tenant->isOnTrial()) {
                            $daysLeft = ceil($tenant->daysLeftInTrial());
                            if ($daysLeft <= 3) {
                                $showWarning = true;
                                $warningMessage = "Tu periodo de prueba termina en {$daysLeft} días. ¡Suscríbete ahora para no perder acceso!";
                            }
                        } elseif ($tenant->subscription_status === 'active' && $tenant->subscription_ends_at) {
                            $daysLeft = now()->diffInDays($tenant->subscription_ends_at, false);
                            if ($daysLeft <= 3 && $daysLeft >= 0) {
                                $showWarning = true;
                                $daysLeft = ceil($daysLeft);
                                $warningMessage = "Tu suscripción vence en {$daysLeft} días. Renueva pronto.";
                            }
                        } elseif (session()->has('warning')) {
                            $showWarning = true;
                            $warningMessage = session('warning');
                            $warningType = 'danger';
                        }
                    }
                @endphp

                @if($showWarning)
                    <div class="{{ $warningType === 'danger' ? 'bg-red-600' : 'bg-yellow-500' }} text-white px-4 py-3 shadow-md relative z-10 flex justify-between items-center">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span class="font-medium">{{ $warningMessage }}</span>
                        </div>
                        <a href="{{ route('billing.subscribe', ['plan' => ($tenant ? $tenant->plan : 'profesional')]) }}" class="bg-white text-gray-900 px-4 py-1 rounded-full text-sm font-bold hover:bg-gray-100 transition">
                            Renovar / Pagar
                        </a>
                    </div>
                @endif

                @if(session()->has('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 m-4" role="alert">
                        <p class="font-bold">Error</p>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @if(session()->has('message'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 m-4" role="alert">
                        <p class="font-bold">Éxito</p>
                        <p>{{ session('message') }}</p>
                    </div>
                @endif

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 custom-scrollbar">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <!-- Notifications Toast -->
        <div x-data="{ show: false, message: '' }" 
             x-on:notify.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
             class="fixed bottom-5 right-5 z-50">
            <div x-show="show" x-transition.duration.300ms 
                 class="bg-gray-800 text-white px-6 py-3 rounded-lg shadow-xl flex items-center">
                <svg class="w-5 h-5 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span x-text="message" class="font-medium"></span>
            </div>
        </div>

        @stack('scripts')
        <livewire:welcome-overlay />
    </body>
</html>
