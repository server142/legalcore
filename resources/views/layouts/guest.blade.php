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
                padding: 2rem 1rem;
                min-height: 100vh;
            }
            
            .app-container {
                background: white;
                border-radius: 4rem;
                width: 100%;
                max-width: 1100px;
                position: relative;
                box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.03);
                display: flex;
                flex-direction: column;
                min-height: calc(100vh - 4rem);
            }

            .inner-form-card {
                background: white;
                border-radius: 2.5rem;
                width: 100%;
                max-width: 440px;
                padding: 2rem;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.04);
                position: relative;
                z-index: 20;
                margin: 2rem 0;
            }

            .illustration-layer {
                position: absolute;
                bottom: 5%;
                left: 5%;
                pointer-events: none;
                z-index: 10;
            }
            
            .illustration-layer img {
                max-height: 320px;
                opacity: 1;
            }

            .btn-primary-custom {
                background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
                color: white;
                box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.3);
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
                box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.05);
            }

            .social-circle {
                width: 44px;
                height: 44px;
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
                <div class="flex-1 flex items-center justify-center p-4 relative min-h-[500px]">
                    
                    <!-- Illustration Layer (Background - Floating Left) -->
                    <div class="illustration-layer hidden md:block">
                        <img src="{{ asset('assets/img/auth-illustration.png') }}" alt="Illustration">
                    </div>

                    <!-- Form Card (Floating on Top) -->
                    <div class="inner-form-card">
                        {{ $slot }}
                    </div>

                </div>

                <!-- Corner Texts / Footer inside -->
                <div class="p-8 text-center md:text-right">
                    <span class="text-[10px] font-bold text-slate-300 pointer-events-none uppercase tracking-widest">DIOGENES &copy; 2026 . Todos los derechos reservados</span>
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
