<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Características - Diogenes</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                    <a href="{{ route('features') }}" class="text-indigo-600 font-semibold transition">Características</a>
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
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Funcionalidad Completa para Despachos Modernos</h1>
            <p class="text-xl opacity-90 max-w-3xl mx-auto">Descubre cómo Diogenes utiliza la última tecnología para optimizar cada aspecto de tu práctica legal.</p>
        </div>
    </section>

    <!-- AI Power -->
    <section class="py-20 px-4 bg-white">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full font-bold text-sm mb-4">NUEVO</div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Asistente Jurídico IA: Diogenes</h2>
                <p class="text-lg text-gray-600 mb-6 leading-relaxed">Olvídate de las búsquedas tediosas. Nuestro asistente de Inteligencia Artificial está entrenado en legislación mexicana y jurisprudencia.</p>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-gray-700"><strong>Redacción Automática:</strong> Genera borradores de demandas, contratos y oficios en segundos.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-gray-700"><strong>Análisis de Sentencias:</strong> Sube un PDF y obtén un resumen ejecutivo con puntos clave y estrategia legal.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="text-gray-700"><strong>Chat 24/7:</strong> Resuelve dudas rápidas sobre procedimientos y plazos a cualquier hora.</span>
                    </li>
                </ul>
            </div>
            <div class="rounded-2xl shadow-2xl overflow-hidden border border-gray-100 transform rotate-1 hover:rotate-0 transition duration-500">
                <!-- Placeholder for UI screenshot -->
                <div class="bg-gray-100 h-96 flex items-center justify-center text-gray-400">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
            </div>
        </div>
    </section>
    
    <!-- New Section: Legal Intelligence (DOF & Jurisprudence) -->
    <section class="py-20 px-4 bg-white">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div class="order-2 md:order-1">
                 <div class="inline-block px-4 py-1.5 bg-green-100 text-green-700 rounded-full font-bold text-sm mb-4">INTELIGENCIA LEGAL</div>
                 <h2 class="text-3xl font-bold text-gray-900 mb-6">Mantente Actualizado Automáticamente</h2>
                 <p class="text-lg text-gray-600 mb-6 leading-relaxed">No pierdas tiempo buscando en múltiples sitios. Diogenes centraliza la información oficial crítica para tu práctica.</p>
                 <ul class="space-y-4">
                     <li class="flex items-start">
                         <svg class="w-6 h-6 text-green-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                         <span class="text-gray-700"><strong>Monitor DOF Diario:</strong> Recibe alertas sobre publicaciones del Diario Oficial de la Federación relevantes para tus materias.</span>
                     </li>
                     <li class="flex items-start">
                         <svg class="w-6 h-6 text-green-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                         <span class="text-gray-700"><strong>Buscador de Jurisprudencia:</strong> Búsqueda semántica inteligente en el Semanario Judicial de la Federación. Encuentra tesis por concepto, no solo por palabra clave.</span>
                     </li>
                     <li class="flex items-start">
                         <svg class="w-6 h-6 text-green-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                         <span class="text-gray-700"><strong>Generador de Contratos Inteligentes:</strong> Crea plantillas con variables dinámicas (ej. nombre, monto) y genera documentos perfectos en segundos.</span>
                     </li>
                 </ul>
            </div>
            <div class="order-1 md:order-2 rounded-2xl shadow-2xl overflow-hidden border border-gray-100 transform rotate-1 hover:rotate-0 transition duration-500">
                <!-- Placeholder for UI screenshot -->
                <div class="bg-indigo-50 h-80 flex items-center justify-center text-indigo-300">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Asesorías Module -->
    <section class="py-20 px-4 bg-gray-50">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
             <div class="order-2 md:order-1 rounded-2xl shadow-2xl overflow-hidden border border-gray-100 transform -rotate-1 hover:rotate-0 transition duration-500">
                <div class="bg-white h-80 flex items-center justify-center text-purple-200">
                    <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div class="order-1 md:order-2">
                <div class="inline-block px-4 py-1.5 bg-purple-100 text-purple-700 rounded-full font-bold text-sm mb-4">CITAS Y CLIENTES</div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Módulo de Asesorías Inteligentes</h2>
                <p class="text-lg text-gray-600 mb-6 leading-relaxed">Profesionaliza la atención a tus clientes desde el primer contacto. Gestiona tus citas y expedientes de asesoría de forma ordenada.</p>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-purple-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-gray-700"><strong>Citas Públicas:</strong> Genera enlaces seguros para que tus clientes consulten los detalles de su cita sin necesidad de registro.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-purple-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-gray-700"><strong>Acceso vía QR:</strong> Facilita el acceso a la información de la asesoría mediante códigos QR dinámicos.</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-purple-500 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-gray-700"><strong>Historial Clínico-Legal:</strong> Convierte una simple asesoría en un Expediente completo con un solo clic si el cliente decide contratarte.</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Case Management -->
    <section class="py-20 px-4 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Control Total de tus Expedientes</h2>
                <p class="text-xl text-gray-600">Desde la demanda inicial hasta la sentencia ejecutoriada.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6 text-blue-600">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="font-bold text-xl mb-3">Expediente Digital Universal</h3>
                    <p class="text-gray-600 mb-4">Almacena ilimitadamente autos, acuerdos, audios y videos. Escritos y promociones en un solo lugar.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Visor de documentos integrado</li>
                        <li class="flex items-center"><svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Búsqueda dentro de PDFs</li>
                    </ul>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6 text-purple-600">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-xl mb-3">Semaforización de Términos</h3>
                    <p class="text-gray-600 mb-4">Nunca se te pasará un plazo. El sistema calcula y alerta visualmente sobre términos fatales.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>Alertas Críticas (Vence Hoy)</li>
                        <li class="flex items-center"><span class="w-2 h-2 rounded-full bg-orange-500 mr-2"></span>Alertas Preventivas (3 días)</li>
                        <li class="flex items-center"><svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Sincronización con Google Calendar</li>
                    </ul>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-6 text-indigo-600">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-xl mb-3">Colaboración en Equipo</h3>
                    <p class="text-gray-600 mb-4">Asigna responsables, comparte notas y mantén a todo tu despacho alineado en un solo expediente.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Historial de Actuaciones</li>
                        <li class="flex items-center"><svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Notas de Inteligencia Artificial guardables</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

     <!-- Financial & Billing -->
     <section class="py-20 px-4 bg-gray-50">
        <div class="max-w-7xl mx-auto text-center">
             <div class="inline-block px-4 py-1.5 bg-yellow-100 text-yellow-700 rounded-full font-bold text-sm mb-4">ADMINISTRACIÓN</div>
             <h2 class="text-3xl font-bold text-gray-900 mb-12">Tus Finanzas bajo Control</h2>
             
             <div class="grid md:grid-cols-4 gap-6">
                 <!-- Feature Item -->
                 <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                     <h3 class="font-bold text-lg mb-2">Control de Honorarios</h3>
                     <p class="text-sm text-gray-500">Registra pagos parciales, saldos pendientes y fechas de cobro.</p>
                 </div>
                 <!-- Feature Item -->
                 <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                     <h3 class="font-bold text-lg mb-2">Reportes de Ingresos</h3>
                     <p class="text-sm text-gray-500">Gráficas en tiempo real de la rentabilidad de tu despacho.</p>
                 </div>
                 <!-- Feature Item -->
                 <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                     <h3 class="font-bold text-lg mb-2">Gastos Operativos</h3>
                     <p class="text-sm text-gray-500">Lleva el registro de viáticos, copias y otros gastos deducibles.</p>
                 </div>
                 <!-- Feature Item -->
                 <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                     <h3 class="font-bold text-lg mb-2">Bitácora de Seguridad</h3>
                     <p class="text-sm text-gray-500">Registro inalterable de todas las acciones realizadas en el sistema (Audit Log).</p>
                 </div>
                 <!-- Feature Item -->
                 <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                     <h3 class="font-bold text-lg mb-2">Portal de Clientes</h3>
                     <p class="text-sm text-gray-500">Permite a tus clientes consultar el estatus de sus expedientes mediante código QR seguro.</p>
                 </div>
             </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 px-4">
        <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-white font-bold text-xl mb-4">Diogenes</h3>
                <p class="text-sm">El sistema de gestión jurídica más completo de México.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Producto</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('features') }}" class="text-white font-semibold">Características</a></li>
                    <li><a href="{{ route('welcome') }}#pricing" class="hover:text-white transition">Precios</a></li>
                    <li><a href="#" class="hover:text-white transition">Seguridad</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Soporte</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('help') }}" class="hover:text-white transition">Centro de Ayuda</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contacto</a></li>
                    <li><a href="https://wa.me/522281405060" target="_blank" class="hover:text-white transition">WhatsApp</a></li>
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
