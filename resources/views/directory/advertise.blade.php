<x-public-layout>
    <div class="relative bg-white overflow-hidden">
        <!-- Navbar (Simplified) -->
        <nav class="absolute top-0 left-0 right-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <a href="/" class="text-2xl font-black tracking-tight text-white drop-shadow-md">DIOGENES</a>
                    <a href="/login" class="text-sm font-bold text-white hover:text-indigo-100 transition-colors">Acceso Miembros</a>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative bg-indigo-900 pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
            <div class="absolute inset-0">
                <img class="w-full h-full object-cover opacity-20" src="https://images.unsplash.com/photo-1505664194779-8beaceb93744?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Legal background">
                <div class="absolute inset-0 bg-indigo-900 mix-blend-multiply"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl mb-6">
                    <span class="block">Haz que los clientes</span>
                    <span class="block text-indigo-400">te encuentren hoy mismo.</span>
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-indigo-100 mb-10">
                    Únete al directorio jurídico de mayor crecimiento. Comienza gratis y destaca tu perfil cuando estés listo para recibir más clientes.
                </p>
                <div class="flex justify-center gap-4">
                    <a href="/register?plan=directory-free" class="px-8 py-4 bg-white text-indigo-900 font-bold rounded-full shadow-xl hover:bg-gray-50 transform hover:scale-105 transition-all text-lg">
                        Publicar Perfil Gratis
                    </a>
                    <a href="#precios" class="hidden md:inline-block px-8 py-4 bg-indigo-800 text-white font-bold rounded-full shadow-lg border border-indigo-700 hover:bg-indigo-700 transition-all text-lg">
                        Ver Planes Premium
                    </a>
                </div>
            </div>
        </div>

        <!-- Benefits Section -->
        <div class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Beneficios Exclusivos</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        Más que un simple directorio
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="text-center">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 text-indigo-600 mx-auto mb-6">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Visibilidad Premium</h3>
                        <p class="text-gray-500">Aparece en los primeros resultados de búsqueda cuando los clientes busquen abogados en tu ciudad y especialidad.</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 text-green-600 mx-auto mb-6">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Contacto Directo</h3>
                        <p class="text-gray-500">Sin intermediarios. Los clientes te contactan directamente a tu WhatsApp, teléfono o correo electrónico.</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 text-purple-600 mx-auto mb-6">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Sello de Verificación</h3>
                        <p class="text-gray-500">Genera confianza inmediata con la insignia de "Abogado Verificado" tras validar tu cédula profesional.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Section -->
        <div id="precios" class="bg-slate-50 py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                 <div class="text-center mb-16">
                    <h2 class="text-3xl font-extrabold text-gray-900">Elige tu Nivel de Visibilidad</h2>
                    <p class="mt-4 text-lg text-gray-500">Comienza a recibir clientes hoy mismo.</p>
                </div>

                <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Free Plan -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-8 text-center h-full flex flex-col">
                            <h3 class="text-xl font-bold text-gray-900">Perfil Básico</h3>
                            <div class="mt-4 flex items-baseline justify-center text-gray-900">
                                <span class="text-4xl font-extrabold tracking-tight">Gratis</span>
                            </div>
                            <p class="mt-4 text-gray-500 text-sm">Para abogados que recién comienzan a tener presencia digital.</p>
                            
                            <ul class="mt-8 space-y-4 text-left flex-grow">
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span class="ml-3 text-base text-gray-700">Listado en Directorio</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="ml-3 text-base text-gray-400">Sin botón de WhatsApp</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="ml-3 text-base text-gray-400">Visibilidad Estándar</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="ml-3 text-base text-gray-400">Sin insignia de verificado</span>
                                </li>
                            </ul>

                            <div class="mt-8">
                                <a href="/register?plan=directory-free" class="block w-full bg-slate-100 border border-transparent rounded-lg py-3 px-6 text-base font-bold text-slate-700 hover:bg-slate-200 transition-all">
                                    Crear Perfil Gratis
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Directory Only Plan -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-indigo-500 transform scale-105 z-10">
                        <div class="bg-indigo-600 py-2 text-center">
                            <span class="text-white text-xs font-bold uppercase tracking-widest">Recomendado</span>
                        </div>
                        <div class="p-8 text-center h-full flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900">Perfil Destacado</h3>
                            <div class="mt-4 flex items-baseline justify-center text-gray-900">
                                <span class="text-5xl font-extrabold tracking-tight">$199</span>
                                <span class="ml-1 text-xl font-semibold text-gray-500">/mes</span>
                            </div>
                            <p class="mt-4 text-gray-500 text-sm">Ideal si solo buscas publicidad y no gestionas expedientes en formato digital.</p>
                            
                            <ul class="mt-8 space-y-4 text-left flex-grow">
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span class="ml-3 text-base text-gray-700"><strong>Prioridad en Búsquedas</strong></span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span class="ml-3 text-base text-gray-700"><strong>Botón Directo a WhatsApp</strong></span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span class="ml-3 text-base text-gray-700">Insignia de Verificado ✅</span>
                                </li>
                                 <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="ml-3 text-base text-gray-400">Sin acceso a gestión de expedientes</span>
                                </li>
                            </ul>

                            <div class="mt-8">
                                <a href="/register?plan=directory-basic" class="block w-full bg-indigo-600 border border-transparent rounded-lg py-3 px-6 text-base font-bold text-white hover:bg-indigo-700 shadow-md transition-all">
                                    Destacar mi Perfil
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Full Suite -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 opacity-80 hover:opacity-100 transition-opacity">
                        <div class="p-8 text-center h-full flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900">Diogenes Suite</h3>
                            <div class="mt-4 flex items-baseline justify-center text-gray-900">
                                <span class="text-5xl font-extrabold tracking-tight">$499</span>
                                <span class="ml-1 text-xl font-semibold text-gray-500">/mes</span>
                            </div>
                            <p class="mt-4 text-gray-500 text-sm">La solución completa para gestionar tu despacho y conseguir clientes.</p>
                            
                            <ul class="mt-8 space-y-4 text-left flex-grow">
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span class="ml-3 text-base text-gray-700"><strong>Todo lo del Plan Destacado</strong></span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span class="ml-3 text-base text-gray-700">Gestión de Expedientes Ilimitados</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span class="ml-3 text-base text-gray-700">Asistente IA (Diogenes)</span>
                                </li>
                                 <li class="flex items-start">
                                    <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span class="ml-3 text-base text-gray-700">Calendario Inteligente</span>
                                </li>
                            </ul>

                            <div class="mt-8">
                                <a href="/register?plan=pro" class="block w-full bg-slate-800 border border-transparent rounded-lg py-3 px-6 text-base font-bold text-white hover:bg-slate-900 transition-all">
                                    Probar Suite Completa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Simplified -->
         <footer class="bg-gray-800 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p>&copy; {{ date('Y') }} Directorio Jurídico Diogenes. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>
</x-public-layout>
