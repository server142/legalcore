<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Aceptación Legal - Diogenes</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            .gradient-bg { 
                background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #4facfe);
                background-size: 400% 400%;
                animation: gradient 15s ease infinite;
            }
            @keyframes gradient {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-slate-100 min-h-screen">
        <div class="min-h-screen flex flex-col items-center justify-center p-4">
            <div class="w-full max-w-5xl">
                <div class="flex justify-center mb-6">
                    <span class="text-3xl font-bold text-slate-700 tracking-wider">DIOGENES</span>
                </div>
                {{ $slot }}
            </div>
        </div>
        @livewireScripts
        @stack('scripts')
    </body>
</html>
