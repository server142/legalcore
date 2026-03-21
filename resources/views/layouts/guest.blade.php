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
            .premium-canvas {
                background-color: #f1f5f9;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 1rem;
            }
            
            .app-container {
                background: white;
                border-radius: 4rem;
                width: 100%;
                max-width: 1000px;
                height: 650px; /* Tamaño estable garantizado */
                position: relative;
                box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.04);
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }

            .inner-form-card {
                background: white;
                border-radius: 2.5rem;
                width: 100%;
                max-width: 400px;
                padding: 2.5rem;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 30;
            }

            .illustration-layer {
                position: absolute;
                bottom: 8%;
                left: 8%;
                pointer-events: none;
                z-index: 10;
            }
            
            .illustration-layer img {
                height: 280px;
                width: auto;
            }

            .btn-primary-custom {
                background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
                color: white;
            }

            .btn-primary-custom:hover {
                background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
                transform: translateY(-1px);
            }

            .input-custom {
                background: #f8fafc;
                border: 1.5px solid #f1f5f9;
                color: #1e293b;
                transition: all 0.3s ease;
            }

            .input-custom:focus {
                background: #ffffff;
                border-color: #4f46e5;
                ring: 0;
                outline: none;
            }

            .social-circle {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                border: 1.5px solid #f1f5f9;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #64748b;
                transition: all 0.3s ease;
            }

            .social-circle:hover {
                background: #f8fafc;
                color: #4f46e5;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="premium-canvas">
            
            <!-- White Main App Container (Red. Style) -->
            <div class="app-container">
                
                <!-- Navbar (Internal) -->
                <div class="flex items-center justify-between px-12 py-8 relative z-30">
                    <a href="/" class="flex items-center gap-3">
                        <img src="{{ asset('favicon.png') }}" alt="Logo" class="w-10 h-auto">
                        <span class="text-xl font-black text-slate-800 tracking-tighter">Diogenes<span class="text-indigo-600">.</span></span>
                    </a>
                    <div class="flex items-center gap-3">
                        <a href="https://wa.me/522281405060" target="_blank" class="social-circle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </a>
                        <a href="https://www.diogenes.com.mx" target="_blank" class="social-circle bg-slate-900 !border-slate-900 !text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- Central Content -->
                <div class="flex-1 relative">
                    
                    <!-- Illustration Layer (Background - Floating Left) -->
                    <div class="illustration-layer hidden md:block">
                        <img src="{{ asset('assets/img/auth-illustration.png') }}" alt="Illustration">
                    </div>

                    <!-- Form Card (Floating on Top - Centered Absoluto) -->
                    <div class="inner-form-card">
                        {{ $slot }}
                    </div>

                    <!-- Right Illustration Elements (Deco) -->
                    <div class="absolute bottom-10 right-10 flex flex-col items-end gap-2 pointer-events-none">
                        <div class="w-24 h-32 bg-yellow-400 opacity-20 rounded-xl"></div>
                        <div class="w-16 h-16 bg-indigo-600 opacity-10 rounded-xl"></div>
                    </div>

                </div>

                <!-- Corner Texts / Footer inside -->
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2">
                    <span class="text-[9px] font-bold text-slate-200 uppercase tracking-widest whitespace-nowrap">DIOGENES &copy; 2026 . GESTIÓN JURÍDICA INTELIGENTE</span>
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
