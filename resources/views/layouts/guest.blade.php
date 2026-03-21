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
            @keyframes gradient {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            
            @keyframes pulse-slow {
                0%, 100% { opacity: 0.3; transform: scale(1); }
                50% { opacity: 0.5; transform: scale(1.1); }
            }
            
            .premium-bg {
                background: radial-gradient(circle at 0% 0%, #1e1b4b 0%, #0f172a 50%, #020617 100%);
                background-size: 200% 200%;
                animation: gradient 15s ease infinite;
            }

            .glow-orb {
                position: absolute;
                width: 600px;
                height: 600px;
                background: radial-gradient(circle, rgba(99,102,241,0.08) 0%, transparent 70%);
                border-radius: 50%;
                filter: blur(60px);
                z-index: 0;
                animation: pulse-slow 10s ease-in-out infinite;
            }
            
            .glass-panel {
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.05);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }
            
            .input-glow:focus {
                box-shadow: 0 0 15px -3px rgba(99, 102, 241, 0.4);
            }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-200">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 premium-bg relative overflow-hidden px-4">
            
            <!-- Dynamic Orbs -->
            <div class="glow-orb" style="top: -200px; left: -200px; background: radial-gradient(circle, rgba(99,102,241,0.12) 0%, transparent 70%);"></div>
            <div class="glow-orb" style="bottom: -200px; right: -200px; background: radial-gradient(circle, rgba(6,182,212,0.08) 0%, transparent 70%); animation-delay: -5s;"></div>

            <div class="w-full {{ $maxWidth ?? 'sm:max-w-md' }} mt-6 px-1 py-1 overflow-hidden sm:rounded-[2.5rem] relative z-10">
                <!-- Subtle Gradient Border -->
                <div class="absolute inset-0 bg-gradient-to-b from-white/10 to-transparent rounded-[2.5rem] -z-10"></div>
                
                <div class="bg-slate-900/40 backdrop-blur-3xl p-8 sm:p-10 rounded-[2.4rem] border border-white/5 shadow-2xl">
                    <div class="flex flex-col items-center mb-10">
                        <!-- Modern Logo -->
                        <a href="/" class="flex items-center gap-3 group">
                            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                            </div>
                            <span class="text-2xl font-black tracking-widest text-white">DIOGENES</span>
                        </a>
                    </div>

                    {{ $slot }}
                </div>
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
