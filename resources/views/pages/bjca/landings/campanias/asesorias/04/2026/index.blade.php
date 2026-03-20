<div class="min-h-screen bg-white overflow-hidden font-['Inter'] antialiased">
    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 h-20 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-xl shadow-lg shadow-indigo-200">
                    BJ
                </div>
                <span class="text-xl font-bold tracking-tight text-gray-900 hidden sm:block">
                    Bufete Jurídico & Consultores Asociados
                </span>
                <span class="text-xl font-bold tracking-tight text-gray-900 sm:hidden">
                    BJCA
                </span>
            </div>
            <div class="flex items-center space-x-6">
                <a href="https://wa.me/5212284076583" target="_blank" class="text-gray-500 hover:text-indigo-600 transition flex items-center gap-2 text-sm font-medium">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.067 2.877 1.215 3.076.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
                <a href="#agendar" class="bg-indigo-600 text-white px-5 py-2.5 rounded-full font-bold text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                    ¡Agendar Ahora!
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 md:pt-48 md:pb-32 px-4 flex flex-col items-center">
        <!-- Background Decor -->
        <div class="absolute inset-0 z-0 h-full w-full pointer-events-none">
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-50 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-purple-50 rounded-full blur-3xl opacity-50 translate-y-1/2 -translate-x-1/2"></div>
        </div>

        <div class="max-w-7xl mx-auto w-full relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 mb-6 px-4 py-1.5 bg-indigo-50 border border-indigo-100 rounded-full text-indigo-700 text-sm font-bold animate-fade-in">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                    </span>
                    EDICIÓN ESPECIAL: {{ strtoupper($this->campaniaMes) }}
                </div>
                <h1 class="text-4xl sm:text-6xl md:text-7xl font-black text-gray-900 leading-[1.1] mb-8">
                    Deja tus problemas legales<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-700">en buenas manos.</span>
                </h1>
                <p class="text-lg md:text-2xl text-gray-600 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                    Sabemos que un proceso judicial puede quitarte el sueño. Durante todo el mes de abril, te regalamos **1 hora de asesoría estratégica** para que tomes el control hoy mismo.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#agendar" class="bg-indigo-600 text-white px-8 py-5 rounded-2xl font-black text-lg hover:shadow-2xl hover:bg-indigo-700 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                        Agendar Mi Asesoría Sin Costo
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <div class="flex flex-col items-center sm:items-start justify-center text-xs text-gray-400 font-medium">
                        <span class="flex items-center gap-1"><svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> Registro Inmediato</span>
                        <span class="flex items-center gap-1"><svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> 100% Confidencial</span>
                    </div>
                </div>
            </div>

            <!-- Featured Campaign Poster -->
            <div class="mt-20 relative px-4 max-w-4xl mx-auto group">
                <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                <img src="{{ asset($this->campaniaPoster) }}" 
                     alt="{{ $this->campaniaNombre }}" 
                     class="relative w-full h-auto rounded-[2rem] shadow-2xl border-2 border-white/50">
            </div>
        </div>
    </section>

    <!-- Why Us Section -->
    <section class="py-24 bg-gray-50/50 relative">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">¿Por qué agendar con nosotros?</h2>
            <p class="text-gray-500 mb-16 max-w-xl mx-auto">Damos el trato humano y profesional que tu caso merece.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="group p-8 bg-white rounded-3xl border border-gray-100 hover:border-indigo-200 hover:shadow-2xl hover:shadow-indigo-100 transition duration-500">
                    <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-indigo-600 transition duration-500">
                        <svg class="w-8 h-8 text-indigo-600 group-hover:text-white transition duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-3.642A9.963 9.963 0 0010 18a9.963 9.963 0 006.44-2.142m-9.44-3.642a4.992 4.992 0 01-4.096-2.433m13.536 2.433a4.992 4.992 0 014.096-2.433M15 8a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-3">Expertos Calificados</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">No te atiende un pasante. Hablarás directamente con abogados especialistas en tu materia.</p>
                </div>

                <!-- Card 2 -->
                <div class="group p-8 bg-white rounded-3xl border border-gray-100 hover:border-purple-200 hover:shadow-2xl hover:shadow-purple-100 transition duration-500">
                    <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-600 transition duration-500">
                        <svg class="w-8 h-8 text-purple-600 group-hover:text-white transition duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-3">Revisión de Casos</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">¿Ya tienes un juicio? Trae tu expediente. Lo analizamos y te damos una opinión honesta sobre tus posibilidades.</p>
                </div>

                <!-- Card 3 -->
                <div class="group p-8 bg-white rounded-3xl border border-gray-100 hover:border-emerald-200 hover:shadow-2xl hover:shadow-emerald-100 transition duration-500">
                    <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 transition duration-500">
                        <svg class="w-8 h-8 text-emerald-600 group-hover:text-white transition duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-3">Presencial o Remoto</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">¿No puedes venir a la oficina? Agendamos una videollamada segura para que no pierdas tiempo en traslados.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section id="agendar" class="py-24 px-4 bg-white relative">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-12 lg:gap-24 items-start">
            <!-- Text Content -->
            <div class="md:w-[45%] sticky top-24">
                <div class="mb-4">
                    <span class="text-indigo-600 font-black text-sm uppercase tracking-widest">Toma Acción</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-8 leading-tight">
                    Tu tranquilidad está<br>a un formulario de distancia.
                </h2>
                <div class="space-y-8">
                    <div class="flex gap-6">
                        <div class="text-2xl font-black text-gray-200">01</div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-2">Llena tus datos actuales</h4>
                            <p class="text-gray-500 text-sm">Necesitamos contactarte rápidamente para confirmar.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="text-2xl font-black text-gray-200">02</div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-2">Cuéntanos tu problema</h4>
                            <p class="text-gray-500 text-sm">Resumir tu caso nos ayuda a asignar al abogado ideal antes de que llegues.</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="text-2xl font-black text-gray-200">03</div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-2">¡Nos vemos pronto!</h4>
                            <p class="text-gray-500 text-sm">Recibirás un correo y una llamada de confirmación.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- The Form Card -->
            <div class="md:w-[55%] w-full">
                <!-- Urgency Banner -->
                <div class="mb-6 flex items-center justify-between p-4 bg-orange-50 border border-orange-100 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                        </span>
                        <span class="text-xs font-bold text-orange-700 uppercase tracking-tighter">Solo quedan 6 espacios disponibles para esta semana</span>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 md:p-12 rounded-[2.5rem] border border-gray-100 relative group overflow-hidden shadow-2xl shadow-indigo-50">
                    <!-- Internal Decor -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-100/50 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                    
                    @if($success)
                        <div class="relative z-10 text-center py-20">
                            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-8 animate-success-pop">
                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="text-3xl font-black text-gray-900 mb-4">¡Todo listo!</h3>
                            <p class="text-gray-600 mb-10 text-lg">{{ $message }}</p>
                            <button wire:click="$set('success', false)" class="px-8 py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition shadow-xl shadow-indigo-100">Agendar otra</button>
                            
                            <!-- Facebook Conversion Tracking -->
                            <script>
                                if (typeof fbq !== 'undefined') {
                                    fbq('track', 'Lead', {
                                        content_name: 'Asesoría Gratis Abril 2026',
                                        content_category: 'Legal Lead'
                                    });
                                }
                            </script>
                        </div>
                    @else
                        <div class="relative z-10">
                            <form wire:submit.prevent="schedule" class="space-y-8">
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-indigo-500 uppercase tracking-widest px-1">Nombre Completo</label>
                                    <input type="text" wire:model.defer="nombre" class="w-full bg-white border-0 ring-1 ring-gray-200 rounded-2xl px-6 py-5 focus:ring-2 focus:ring-indigo-600 transition shadow-sm h-16 text-gray-900" placeholder="Escribe tu nombre..." required>
                                    @error('nombre') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-indigo-500 uppercase tracking-widest px-1">Email</label>
                                        <input type="email" wire:model.defer="email" class="w-full bg-white border-0 ring-1 ring-gray-200 rounded-2xl px-6 py-5 focus:ring-2 focus:ring-indigo-600 transition shadow-sm h-16 text-gray-900" placeholder="tu@correo.com" required>
                                        @error('email') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-indigo-500 uppercase tracking-widest px-1">WhatsApp / Teléfono</label>
                                        <input type="tel" wire:model.defer="telefono" class="w-full bg-white border-0 ring-1 ring-gray-200 rounded-2xl px-6 py-5 focus:ring-2 focus:ring-indigo-600 transition shadow-sm h-16 text-gray-900" placeholder="(10 dígitos)" required>
                                        @error('telefono') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Tipo de Asesoría -->
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-indigo-500 uppercase tracking-widest px-1">¿Cómo prefieres la asesoría?</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- Presencial -->
                                        <label class="relative flex cursor-pointer rounded-2xl border-2 p-4 focus:outline-none transition-all {{ $tipo === 'presencial' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 bg-white hover:border-indigo-300' }}">
                                            <input type="radio" wire:model.live="tipo" value="presencial" class="sr-only">
                                            <span class="flex flex-1">
                                                <span class="flex flex-col text-left">
                                                    <span class="block text-sm font-black text-gray-900">🏛️ Presencial</span>
                                                    <span class="mt-1 flex items-center text-xs text-gray-500 font-medium italic">En nuestras oficinas</span>
                                                </span>
                                            </span>
                                            @if($tipo === 'presencial')
                                                <svg class="h-6 w-6 text-indigo-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                            @endif
                                        </label>
                                        
                                        <!-- En Línea -->
                                        <label class="relative flex cursor-pointer rounded-2xl border-2 p-4 focus:outline-none transition-all {{ $tipo === 'videoconferencia' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 bg-white hover:border-indigo-300' }}">
                                            <input type="radio" wire:model.live="tipo" value="videoconferencia" class="sr-only">
                                            <span class="flex flex-1">
                                                <span class="flex flex-col text-left">
                                                    <span class="block text-sm font-black text-gray-900">💻 En Línea</span>
                                                    <span class="mt-1 flex items-center text-xs text-gray-500 font-medium italic">Videollamada / Zoom</span>
                                                </span>
                                            </span>
                                            @if($tipo === 'videoconferencia')
                                                <svg class="h-6 w-6 text-indigo-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                            @endif
                                        </label>
                                    </div>
                                    @error('tipo') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-black text-indigo-500 uppercase tracking-widest px-1">¿Qué tema legal te preocupa?</label>
                                    <textarea wire:model.defer="asunto" rows="3" class="w-full bg-white border-0 ring-1 ring-gray-200 rounded-2xl px-6 py-5 focus:ring-2 focus:ring-indigo-600 transition shadow-sm text-gray-900" placeholder="Ej: Divorcio, Demanda Laboral, Revisión de Contrato..." required></textarea>
                                    @error('asunto') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid md:grid-cols-2 gap-8">
                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-indigo-500 uppercase tracking-widest px-1">Fecha de preferencia</label>
                                        <input type="date" wire:model.live="fecha" min="2026-04-01" max="2026-04-30" class="w-full bg-white border-0 ring-1 ring-gray-200 rounded-2xl px-6 py-5 focus:ring-2 focus:ring-indigo-600 transition shadow-sm h-16 text-gray-900" required>
                                        @error('fecha') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-indigo-500 uppercase tracking-widest px-1">Hora sugerida</label>
                                        
                                        <div class="relative min-h-[4rem]">
                                            <div wire:loading wire:target="fecha" class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl">
                                                <svg class="animate-spin h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </div>

                                            @if(empty($availableSlots))
                                                <div class="p-4 bg-red-50 text-red-600 text-xs font-bold rounded-2xl border border-red-100 italic">
                                                    No hay horarios disponibles para esta fecha. Intenta con otro día.
                                                </div>
                                            @else
                                                <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                                    @foreach($availableSlots as $slot)
                                                        <button type="button" 
                                                                wire:click="selectSlot('{{ $slot['value'] }}')"
                                                                class="py-3 px-4 rounded-xl text-sm font-bold transition-all border {{ $hora == $slot['value'] ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white border-gray-100 text-gray-600 hover:border-indigo-300 hover:bg-indigo-50' }}">
                                                            {{ $slot['label'] }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        @error('hora') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <button type="submit" wire:loading.attr="disabled" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-800 text-white font-black py-6 rounded-2xl shadow-xl shadow-indigo-200 hover:shadow-2xl hover:translate-y-[-4px] transition duration-300 flex items-center justify-center gap-4 text-xl group">
                                    <span wire:loading.remove>¡RESERVAR MI HORA GRATIS!</span>
                                    <span wire:loading>PROCESANDO...</span>
                                    <svg class="w-6 h-6 group-hover:translate-x-2 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                                
                                <div class="text-center space-y-4">
                                    <div class="flex items-center justify-center gap-6 opacity-60 grayscale hover:grayscale-0 transition duration-500">
                                        <div class="flex items-center gap-1 text-[8px] font-bold uppercase tracking-tighter">
                                            <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                            Datos Encriptados
                                        </div>
                                        <div class="flex items-center gap-1 text-[8px] font-bold uppercase tracking-tighter">
                                            <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                            No Spam
                                        </div>
                                        <div class="flex items-center gap-1 text-[8px] font-bold uppercase tracking-tighter">
                                            <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                            Sin Compromiso
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 font-medium px-4 leading-tight">
                                        Al enviar este formulario, aceptas que **Bufete Jurídico & Consultores Asociados** te contacte para confirmar tu cita. Tus datos están protegidos por nuestro Aviso de Privacidad.
                                    </p>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Area -->
    <section class="py-20 bg-gray-900 overflow-hidden relative">
        <div class="absolute inset-0 z-0 opacity-10 pointer-events-none">
            <svg class="h-full w-full" fill="none" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0, 50 0, 100 100" stroke="white" stroke-width="0.1" fill="none"/>
            </svg>
        </div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-3xl md:text-5xl font-black text-white mb-8">No permitas que el tiempo se agote.</h2>
            <p class="text-gray-400 text-lg md:text-xl font-light mb-12">Esta campaña solo está disponible durante el mes de abril de 2026. Los espacios se llenan rápido.</p>
            <a href="#agendar" class="inline-block bg-white text-gray-900 px-10 py-5 rounded-2xl font-black text-xl hover:bg-gray-100 transition shadow-2xl">
                Asegurar Mi Espacio Hoy
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center text-sm text-gray-400 font-medium space-y-6 md:space-y-0">
            <div class="flex items-center gap-4">
                <span class="text-gray-900 font-black tracking-widest">BJCA</span>
                <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                <span>Justicia a tu alcance.</span>
            </div>
            <div class="flex gap-8">
                <a href="{{ route('privacy') }}" class="hover:text-indigo-600 transition">Aviso de Privacidad</a>
                <a href="{{ route('terms') }}" class="hover:text-indigo-600 transition">Términos de Servicio</a>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/5212284076583?text=Hola,%20quisiera%20más%20información%20sobre%20la%20campaña%20de%20asesorías%20gratuitas" 
       target="_blank" 
       class="fixed bottom-8 right-8 z-[100] bg-[#25D366] text-white p-4 rounded-full shadow-2xl hover:scale-110 active:scale-95 transition-all group flex items-center gap-3">
        <span class="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-xs transition-all duration-500 font-bold text-sm">¿Dudas? Escríbenos</span>
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.067 2.877 1.215 3.076.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>

    <style>
        @keyframes fade-in { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }
        @keyframes success-pop { 0% { transform: scale(0.5); opacity: 0; } 80% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
        .animate-fade-in { animation: fade-in 1s ease-out forwards; }
        .animate-success-pop { animation: success-pop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .animate-bounce-slow { animation: bounce 3s infinite; }
        .animate-pulse-slow { animation: pulse 4s infinite; }
        html { scroll-behavior: smooth; }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</div>
