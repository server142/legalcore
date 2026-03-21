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
                background-color: #f8fafc;
                background-image: url('{{ asset('assets/img/auth-bg.png') }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }
            
            .auth-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.05), 0 30px 60px -30px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.5);
            }

            .btn-primary-custom {
                background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
                color: white;
                box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.3);
            }

            .btn-primary-custom:hover {
                background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
                transform: translateY(-1px);
                box-shadow: 0 15px 25px -5px rgba(79, 70, 229, 0.4);
            }

            .input-custom {
                background: #f1f5f9;
                border: 2px solid transparent;
                color: #1e293b;
                transition: all 0.3s ease;
            }

            .input-custom:focus {
                background: #ffffff;
                border-color: #4f46e5;
                ring: 0;
                outline: none;
                box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-800">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-12 premium-bg relative px-4">
            
            <div class="w-full {{ $maxWidth ?? 'sm:max-w-xl md:max-w-4xl' }} relative z-10 auth-card rounded-[3rem] p-8 sm:p-16 flex flex-col md:flex-row gap-16 items-center">
                
                <div class="hidden md:flex flex-1 flex-col justify-center">
                    <div class="mb-12">
                        <!-- Img Logo Diogenes -->
                        <img src="{{ asset('favicon.png') }}" alt="Diogenes Logo" class="w-48 h-auto mb-6">
                        <h1 class="text-4xl font-black text-slate-900 leading-tight">Gestión Jurídica<br><span class="text-indigo-600">Inteligente</span></h1>
                    </div>
                    <p class="text-slate-500 font-medium mb-8">El sistema de gestión más completo para abogados que buscan eficiencia y organización en un solo lugar.</p>
                    <div class="flex items-center gap-4 text-xs font-bold text-slate-400">
                        <span class="flex items-center gap-1"><svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> 15 Días Prueba Gratis</span>
                        <span class="flex items-center gap-1"><svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Soporte Premium</span>
                    </div>
                </div>

                <div class="w-full md:w-[450px]">
                    <div class="md:hidden flex flex-col items-center mb-10">
                        <img src="{{ asset('favicon.png') }}" alt="Diogenes Logo" class="w-32 h-auto">
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
