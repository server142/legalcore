<div>
    <!-- Navigation (Replicated from landing for consistency) -->
    <nav class="bg-white shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Diogenes</a>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('welcome') }}#features" class="text-gray-700 hover:text-indigo-600 transition">Características</a>
                    <a href="{{ route('welcome') }}#pricing" class="text-gray-700 hover:text-indigo-600 transition">Precios</a>
                    <a href="{{ route('contact') }}" class="text-indigo-600 font-semibold transition">Contacto</a>
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
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Contáctanos</h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto">Estamos aquí para ayudarte. Si tienes dudas sobre el sistema o necesitas soporte especializado, no dudes en escribirnos.</p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="py-20 px-4 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-12">
            <!-- Info Column -->
            <div class="space-y-8">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Información de Contacto</h3>
                    <p class="text-gray-600 mb-6">Nuestro equipo de soporte está disponible de Lunes a Viernes de 9:00 AM a 6:00 PM.</p>
                </div>

                <!-- Dynamic WhatsApp Block -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start space-x-4 hover:shadow-md transition">
                    <div class="bg-green-100 p-3 rounded-lg text-green-600">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">WhatsApp</h4>
                        <p class="text-sm text-gray-500 mb-2">Respuesta rápida</p>
                        <a href="{{ $supportWhatsappUrl }}" target="_blank" class="text-indigo-600 font-semibold hover:underline">Iniciar chat &rarr;</a>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start space-x-4 hover:shadow-md transition">
                    <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Correo Electrónico</h4>
                        <p class="text-sm text-gray-500 mb-2">Consultas generales</p>
                        <a href="mailto:{{ $supportEmail }}" class="text-indigo-600 font-semibold hover:underline">{{ $supportEmail }}</a>
                    </div>
                </div>
            </div>

            <!-- Form Column -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Envíanos un mensaje</h3>
                
                @if($successMessage)
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg border border-green-200 flex items-start animate-fade-in-up">
                        <svg class="w-5 h-5 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="font-bold">¡Mensaje Enviado!</p>
                            <p class="text-sm">{{ $successMessage }}</p>
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="submit" class="space-y-4 relative">
                     <!-- Loading Overlay -->
                    <div wire:loading.flex wire:target="submit" class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 items-center justify-center rounded-lg">
                        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                        <input wire:model="name" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror" placeholder="Tu nombre">
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input wire:model="email" type="email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror" placeholder="tucorreo@ejemplo.com">
                        @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                        <select wire:model="subject" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option>Soporte Técnico</option>
                            <option>Ventas / Planes</option>
                            <option>Facturación</option>
                            <option>Otro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                        <textarea wire:model="message" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('message') border-red-500 @enderror" placeholder="Describe tu consulta..."></textarea>
                        @error('message') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition shadow-md disabled:opacity-50">Enviar Mensaje</button>
                </form>
            </div>
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
                    <li><a href="{{ $supportWhatsappUrl }}" target="_blank" class="hover:text-white transition">WhatsApp</a></li>
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
</div>
