<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

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
            
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .gradient-bg { 
                background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #4facfe);
                background-size: 400% 400%;
                animation: gradient 15s ease infinite;
            }
            
            .float-animation {
                animation: float 6s ease-in-out infinite;
            }
            
            .fade-in-up {
                animation: fadeInUp 0.8s ease-out;
            }
            
            .glass-effect {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .pattern-dots {
                background-image: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
                background-size: 20px 20px;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900 gradient-bg relative overflow-hidden pattern-dots px-4">
            <!-- Floating Shapes -->
            <div class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full blur-3xl float-animation"></div>
            <div class="absolute bottom-20 right-20 w-40 h-40 bg-white/10 rounded-full blur-3xl float-animation" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/3 w-24 h-24 bg-white/10 rounded-full blur-2xl float-animation" style="animation-delay: 4s;"></div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg glass-effect fade-in-up relative z-10">
                <div class="flex justify-center mb-8">
                    <!-- Logo -->
                    <a href="/" class="text-3xl font-bold text-gray-900 tracking-wider hover:text-indigo-600 transition">
                        DIOGENES
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
