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
            .premium-bg {
                background-color: #f3f4f6;
                background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
                background-size: 20px 20px;
            }
            
            .auth-card {
                background: #ffffff;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
                border: 1px solid #f1f5f9;
            }

            .btn-primary-custom {
                background: #f07e3e;
                color: white;
                box-shadow: 0 10px 15px -3px rgba(240, 126, 62, 0.2);
            }

            .btn-primary-custom:hover {
                background: #e06c2c;
                transform: translateY(-1px);
            }

            .input-custom {
                background: #f8fafc;
                border: 1.5px solid #f1f5f9;
                color: #1e293b;
            }

            .input-custom:focus {
                background: #ffffff;
                border-color: #f07e3e;
                ring: 0;
                outline: none;
                box-shadow: 0 0 0 4px rgba(240, 126, 62, 0.1);
            }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-800">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-12 premium-bg relative px-4">
            
            <div class="w-full {{ $maxWidth ?? 'sm:max-w-md' }} relative z-10 auth-card rounded-[2rem] p-8 sm:p-12">
                <div class="flex flex-col items-center mb-10">
                    <!-- Modern Clean Logo -->
                    <a href="/" class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-[#f07e3e] rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-500/10">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        </div>
                        <span class="text-2xl font-black tracking-tighter text-slate-900">Diogenes<span class="text-[#f07e3e]">.</span></span>
                    </a>
                </div>

                {{ $slot }}
            </div>
            
            <!-- Footer Links -->
            <div class="mt-8 text-center text-xs text-white/80 relative z-10">
                <p>
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </body>
</html>
