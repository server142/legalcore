@php
    $whatsappUrl = \Illuminate\Support\Facades\DB::table('global_settings')->where('key', 'support_whatsapp_url')->value('value') ?? 'https://wa.me/522281405060';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Ayuda - Diogenes</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Diogenes</a>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('welcome') }}#features" class="text-gray-700 hover:text-indigo-600 transition">Características</a>
                    <a href="{{ route('welcome') }}#pricing" class="text-gray-700 hover:text-indigo-600 transition">Precios</a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-indigo-600 transition">Contacto</a>
                </div>
                <div class="flex space-x-4">
                    <a href="/login" class="text-gray-700 hover:text-indigo-600 transition font-medium">Iniciar Sesión</a>
                    <a href="/register?plan=trial" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">Comenzar Gratis</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <section class="gradient-bg pt-32 pb-20 px-4">
        <div class="max-w-7xl mx-auto text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Centro de Ayuda</h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto">Encuentra respuestas rápidas, tutoriales y guías para sacar el máximo provecho a Diogenes.</p>
            
            <div class="mt-8 max-w-xl mx-auto">
                <div class="relative">
                    <input type="text" placeholder="¿Cómo podemos ayudarte hoy?" class="w-full py-4 px-6 pr-12 rounded-full text-gray-800 shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <button class="absolute right-4 top-1/2 transform -translate-y-1/2 text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-20 px-4 max-w-7xl mx-auto">
        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md transition cursor-pointer border border-gray-100 text-center">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="font-bold text-lg mb-2">Primeros Pasos</h3>
                <p class="text-sm text-gray-500">Configuración inicial, creación de cuenta y tour básico.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md transition cursor-pointer border border-gray-100 text-center">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="font-bold text-lg mb-2">Gestión de Expedientes</h3>
                <p class="text-sm text-gray-500">Cómo crear, editar y archivar tus casos legales.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-md transition cursor-pointer border border-gray-100 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <h3 class="font-bold text-lg mb-2">Facturación y Pagos</h3>
                <p class="text-sm text-gray-500">Dudas sobre planes, facturas y métodos de pago.</p>
            </div>
        </div>

        <!-- FAQ Accordion -->
        <div class="max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Preguntas Frecuentes</h2>
            
            <div class="space-y-4" x-data="{ active: null }">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <button @click="active = (active === 1 ? null : 1)" class="w-full px-6 py-4 text-left flex justify-between items-center bg-white hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-800">¿Cómo funciona la prueba gratuita?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="active === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="active === 1" x-collapse>
                        <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
                            Tienes 15 días de acceso total a todas las funcionalidades del plan 'Bufete Pro'. No necesitas ingresar tarjeta de crédito. Al finalizar, puedes decidir si contratar o continuar con una cuenta limitada gratuita.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <button @click="active = (active === 2 ? null : 2)" class="w-full px-6 py-4 text-left flex justify-between items-center bg-white hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-800">¿Mis datos están seguros?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="active === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="active === 2" x-collapse>
                        <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
                            Absolutamente. Utilizamos encriptación de nivel bancario (AES-256) para todos tus documentos y expedientes. Realizamos copias de seguridad diarias y nuestros servidores cumplen con los estándares de seguridad más estrictos.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <button @click="active = (active === 3 ? null : 3)" class="w-full px-6 py-4 text-left flex justify-between items-center bg-white hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-800">¿Puedo cancelar mi suscripción?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="active === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="active === 3" x-collapse>
                        <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
                            Sí, puedes cancelar en cualquier momento desde tu panel de facturación. No hay plazos forzosos ni penalizaciones. Tu acceso se mantendrá hasta el final del periodo pagado.
                        </div>
                    </div>
                </div>

                 <!-- FAQ Item 4 -->
                 <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <button @click="active = (active === 4 ? null : 4)" class="w-full px-6 py-4 text-left flex justify-between items-center bg-white hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-800">¿Necesito instalar algún software?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="active === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="active === 4" x-collapse>
                        <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
                            No. Diogenes funciona 100% en la nube. Solo necesitas un navegador web (Chrome, Edge, Safari) y conexión a internet para acceder desde tu computadora, tablet o celular.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-12">
            <p class="text-gray-600 mb-4">¿No encuentras lo que buscas?</p>
            <a href="{{ route('contact') }}" class="inline-block bg-white text-indigo-600 px-6 py-2 rounded-lg font-bold border border-indigo-200 hover:shadow-md transition">Contactar a Soporte</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 px-4 mt-12">
        <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-white font-bold text-xl mb-4">Diogenes</h3>
                <p class="text-sm">El sistema de gestión jurídica más completo de México.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Producto</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('welcome') }}#features" class="hover:text-white transition">Características</a></li>
                    <li><a href="{{ route('welcome') }}#pricing" class="hover:text-white transition">Precios</a></li>
                    <li><a href="#" class="hover:text-white transition">Seguridad</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Soporte</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('help') }}" class="hover:text-white transition">Centro de Ayuda</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contacto</a></li>
                    <li><a href="{{ $whatsappUrl }}" target="_blank" class="hover:text-white transition">WhatsApp</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Legal</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('terms') }}" class="hover:text-white transition">Términos y Condiciones</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Aviso de Privacidad</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto mt-12 pt-8 border-t border-gray-800 text-center text-sm">
            <p>&copy; 2026 Diogenes. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
