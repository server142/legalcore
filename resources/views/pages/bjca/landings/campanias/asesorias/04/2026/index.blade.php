<div class="min-h-screen bg-gray-50 flex flex-col items-center">
    <!-- Header with logo and back-to-site info -->
    <header class="w-full bg-white shadow-sm py-4 px-6 md:px-12 flex justify-between items-center fixed top-0 z-40">
        <div class="flex items-center">
            <span class="text-2xl font-bold bg-gradient-to-r from-indigo-700 to-purple-800 bg-clip-text text-transparent">
                Bufete Jurídico & Consultores Asociados
            </span>
        </div>
        <div>
            <a href="/" class="text-sm text-gray-600 hover:text-indigo-600 font-medium">Sitio Principal</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:flex lg:items-center lg:gap-16">
                <!-- Left Column: Information -->
                <div class="lg:w-1/2 mb-12 lg:mb-0">
                    <div class="inline-block mb-4 px-4 py-2 bg-indigo-100 rounded-full text-indigo-700 text-sm font-bold uppercase tracking-wider">
                        Campaña Abril 2026
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                        Mes de Asesoría Jurídica <br>
                        <span class="text-indigo-600">Totalmente Gratis</span>
                    </p>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Durante todo el mes de abril, nuestro despacho abre sus puertas para ayudarte a resolver tus dudas legales. <br><br>
                        Ofrecemos **hasta 1 hora de asesoría personalizada** completamente gratis. Ya sea para iniciar un nuevo asunto, resolver dudas jurídicas o revisar procesos legales en ejecución.
                    </p>

                    <ul class="space-y-4 mb-10 text-gray-700">
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Derecho Familiar, Civil, Mercantil y Laboral.</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Revisión de expedientes y juicios actuales.</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Abogados especialistas con amplia experiencia.</span>
                        </li>
                    </ul>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 inline-block">
                        <div class="text-sm font-bold text-gray-500 mb-2 uppercase tracking-wide">Despacho Responsable</div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                                BJ
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">Bufete Jurídico & Consultores Asociados</div>
                                <div class="text-sm text-gray-500">Xalapa, Veracruz / Remoto</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Scheduling Form -->
                <div class="lg:w-1/2">
                    <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
                        @if($success)
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-4">¡Agendado Exitosamente!</h2>
                                <p class="text-gray-600 mb-8">{{ $message }}</p>
                                <button wire:click="$set('success', false)" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition">Regresar al Formulario</button>
                            </div>
                        @else
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Agénda tu Asesoría Aquí</h2>
                            <p class="text-gray-500 mb-8">Completa el formulario y selecciona tu horario preferido.</p>

                            <form wire:submit.prevent="schedule" class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo</label>
                                    <input type="text" wire:model.defer="nombre" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition" placeholder="Tu nombre..." required>
                                    @error('nombre') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                                        <input type="email" wire:model.defer="email" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition" placeholder="tu@correo.com" required>
                                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono de Contacto</label>
                                        <input type="tel" wire:model.defer="telefono" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition" placeholder="(228) 000 0000" required>
                                        @error('telefono') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Breve descripción del asunto / duda</label>
                                    <textarea wire:model.defer="asunto" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition" placeholder="¿En qué podemos apoyarte?" required></textarea>
                                    @error('asunto') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha deseada</label>
                                        <input type="date" wire:model.defer="fecha" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition" required>
                                        @error('fecha') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Hora deseada</label>
                                        <select wire:model.defer="hora" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition" required>
                                            <option value="09:00">09:00 AM</option>
                                            <option value="10:00" selected>10:00 AM</option>
                                            <option value="11:00">11:00 AM</option>
                                            <option value="12:00">12:00 PM</option>
                                            <option value="13:00">01:00 PM</option>
                                            <option value="14:00">02:00 PM</option>
                                            <option value="16:00">04:00 PM</option>
                                            <option value="17:00">05:00 PM</option>
                                            <option value="18:00">06:00 PM</option>
                                        </select>
                                        @error('hora') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <button type="submit" wire:loading.attr="disabled" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 hover:shadow-xl hover:translate-y-[-2px] transition transform disabled:opacity-50">
                                    <span wire:loading.remove>Agendar Asesoría Gratis</span>
                                    <span wire:loading>Procesando...</span>
                                </button>
                                
                                <p class="text-center text-gray-500 text-xs mt-4 italic">
                                    *Sujeto a confirmación por parte del Licenciado asignado.
                                </p>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Landing -->
    <footer class="w-full bg-gray-900 py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-gray-400 text-sm">
            <div class="mb-6 md:mb-0">
                <span class="text-white font-bold text-lg mr-2">Diogenes</span>
                <span>© 2026 - Campaña Especial de Consultoría Legal</span>
            </div>
            <div class="flex gap-8">
                <a href="{{ route('privacy') }}" class="hover:text-white transition">Aviso de Privacidad</a>
                <a href="{{ route('terms') }}" class="hover:text-white transition">Términos del Servicio</a>
            </div>
        </div>
    </footer>
</div>
