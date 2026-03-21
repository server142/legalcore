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
        <link href="https://fonts.bunny.net/css?family=Outfit:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        
        <style>
            body { font-family: 'Outfit', sans-serif; }
            .premium-canvas {
                background-color: #e5e5e5;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 1rem;
            }
            .app-wrapper {
                background: white;
                border-radius: 2rem;
                width: 100%;
                max-width: 1100px;
                position: relative;
                box-shadow: 0 20px 40px rgba(0,0,0,0.03);
            }
            @media (min-width: 768px) {
                .app-wrapper {
                    height: 80vh;
                    min-height: 700px;
                    border-radius: 1.5rem;
                }
            }
            .form-card {
                background: white;
                border-radius: 1rem;
                box-shadow: 0 10px 40px rgba(0,0,0,0.08); /* Like Red. float */
            }
            /* Inputs Red Style */
            .input-flat {
                background-color: #f4f4f5;
                border: 2px solid #f4f4f5;
                border-radius: 1rem;
                font-size: 0.875rem;
                font-weight: 500;
                transition: all 0.2s;
            }
            .input-flat:focus {
                border-color: #e4e4e7;
                background-color: white;
                outline: none;
                box-shadow: none;
                ring: 0;
            }
            .btn-flat {
                background-color: #4f46e5;
                color: white;
                border-radius: 0.75rem;
                font-weight: 600;
                transition: opacity 0.2s;
            }
            .btn-flat:hover { opacity: 0.9; }
            .btn-outline {
                border: 1px solid #e4e4e7;
                border-radius: 0.75rem;
                font-weight: 600;
                color: #3f3f46;
                transition: border-color 0.2s;
            }
            .btn-outline:hover { border-color: #a1a1aa; }
        </style>
    </head>
    <body class="antialiased text-slate-900">
        <div class="premium-canvas">
            
            <div class="app-wrapper flex flex-col md:block">
                
                <!-- Navbar fixed inside -->
                <div class="flex items-center justify-between p-6 md:p-10 md:absolute top-0 left-0 w-full z-40">
                    <a href="/" class="flex items-center gap-3">
                        <img src="{{ asset('favicon.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                        <span class="text-2xl font-bold tracking-tight">Diogenes.</span>
                    </a>
                    
                    <div class="flex items-center gap-3">
                        <a href="https://wa.me/522281405060" class="w-10 h-10 rounded-full border border-slate-300 flex items-center justify-center text-slate-600 hover:border-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </a>
                        <a href="https://www.diogenes.com.mx" class="w-10 h-10 rounded-full bg-slate-900 flex items-center justify-center text-white hover:bg-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- MOBILE FLOW -->
                <div class="md:hidden flex-1 p-6 relative z-30">
                    <div class="form-card px-6 py-8 w-full">
                        {{ $slot }}
                    </div>
                </div>

                <!-- DESKTOP SCENE -->
                <div class="hidden md:block w-full h-full relative z-10">
                    
                    <!-- The "Floor" Line -->
                    <div class="absolute bottom-[25%] left-[10%] right-[10%] border-b border-black"></div>

                    <!-- Left Illustration -->
                    <div class="absolute bottom-[25%] left-[12%] w-[250px] z-20 bg-white" style="margin-bottom: -1px;">
                        <img src="{{ asset('assets/img/auth-illustration.png') }}" class="w-full h-auto mix-blend-multiply opacity-90" alt="">
                    </div>

                    <!-- Right Deco Boxes -->
                    <div class="absolute bottom-[25%] right-[15%] z-20 flex items-end gap-1 bg-white px-2" style="margin-bottom: -1px; padding-bottom: 1px;">
                        <div class="w-20 h-32 border-2 border-black bg-white relative">
                            <!-- fake shadow/depth -->
                            <div class="absolute top-2 -right-2 w-full h-full border-r-2 border-b-2 border-black"></div>
                        </div>
                        <div class="w-24 h-48 border-2 border-black bg-[#fde047] relative">
                             <!-- graphic stroke -->
                             <div class="absolute -top-12 -right-12 w-20 h-20 border-2 border-black rounded-full border-b-transparent border-l-transparent transform rotate-45 opacity-50"></div>
                        </div>
                    </div>

                    <!-- Center Form Card -->
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-[45%] w-full max-w-[420px] form-card p-10 z-30">
                         {{ $slot }}
                    </div>

                </div>

            </div>
            
        </div>
        @livewireScripts
    </body>
</html>
