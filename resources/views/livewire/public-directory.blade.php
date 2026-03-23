<div class="min-h-screen bg-slate-50 font-sans text-slate-900">
    
    <!-- Navbar Minimalista -->
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="flex items-center gap-2 group">
                        <!-- Logo Icon -->
                        <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center text-white group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-slate-900">DIOGENES</span>
                    </a>
                </div>

                <!-- Desktop Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="/login" class="text-sm font-bold text-slate-600 hover:text-slate-900 bg-slate-100/80 hover:bg-slate-200 px-4 py-2 rounded-full transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Acceso Abogados
                    </a>
                    <a href="{{ route('directory.advertise') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 px-4 py-2 rounded-full transition-colors">Unirme al Directorio</a>
                </div>

                <!-- Mobile Buttons Icon (Simplified) -->
                <div class="flex md:hidden items-center gap-2">
                    <a href="/login" class="p-2 text-slate-600 bg-slate-100 rounded-full" title="Login">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    </a>
                    <a href="{{ route('directory.advertise') }}" class="text-xs font-black text-white bg-indigo-600 px-3 py-2 rounded-lg transition-colors shadow-sm">
                        Unirme
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section (Dark Modern) -->
    <div class="relative bg-slate-900 text-white overflow-hidden pb-12 pt-16 lg:pt-24 shadow-xl mb-12 rounded-b-[3rem]">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('assets/img/directory-hero-bg.png') }}" class="w-full h-full object-cover opacity-30 mix-blend-overlay" alt="Background">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/50 via-slate-900/80 to-slate-900"></div>
        </div>
        
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10 z-0" style="background-image: radial-gradient(#4f46e5 1px, transparent 1px); background-size: 32px 32px;"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <span class="inline-block py-1 px-3 rounded-full bg-indigo-500/20 text-indigo-300 text-[10px] font-black tracking-widest uppercase mb-4 border border-indigo-500/30">
                Directorio Oficial Verificado
            </span>
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-black tracking-tight mb-6 leading-tight">
                El abogado experto para<br class="hidden md:block"/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">tu caso específico.</span>
            </h1>

            <!-- Search Area (Enhanced with Matter, State, City) -->
            <div class="mt-10 max-w-5xl mx-auto">
                <div class="bg-white rounded-3xl p-2 lg:p-3 shadow-2xl border-4 border-white/5 backdrop-blur-md">
                    <div class="flex flex-col lg:flex-row items-stretch gap-2">
                        
                        <!-- Materia / Palabra Clave -->
                        <div class="flex-1 relative min-w-0">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="text" 
                                class="block w-full pl-11 pr-4 py-4 text-sm md:text-base border-0 bg-transparent focus:ring-0 text-slate-900 placeholder-slate-400" 
                                placeholder="Especialidad o Nombre (ej. Familiar)">
                        </div>

                        <!-- Estado -->
                        <div class="lg:w-1/4 relative lg:border-l border-slate-100">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                            </div>
                            <select wire:model.live="state" class="block w-full pl-11 pr-10 py-4 text-sm md:text-base border-0 bg-transparent focus:ring-0 text-slate-900 cursor-pointer appearance-none">
                                <option value="">Todo México 🇲🇽</option>
                                @foreach($statesList as $estado)
                                    <option value="{{ $estado }}">{{ $estado }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ciudad -->
                        <div class="lg:w-1/4 relative lg:border-l border-slate-100">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" /></svg>
                            </div>
                            <input wire:model.live.debounce.300ms="city" type="text" 
                                class="block w-full pl-11 pr-4 py-4 text-sm md:text-base border-0 bg-transparent focus:ring-0 text-slate-900 placeholder-slate-400" 
                                placeholder="Tu Ciudad">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Filters (Pills) -->
            <div class="mt-8 flex flex-wrap justify-center gap-2 px-2">
                <button wire:click="$set('specialty', '')" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all {{ $specialty === '' ? 'bg-white text-indigo-600 shadow-lg scale-105' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 border border-slate-700' }}">
                    Todos
                </button>
                @foreach(array_slice($allSpecialties, 0, 6) as $spec)
                    <button wire:click="$set('specialty', '{{ $spec }}')" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all {{ $specialty === $spec ? 'bg-white text-indigo-600 shadow-lg scale-105' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 border border-slate-700' }}">
                        {{ $spec }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="flex justify-between items-end mb-8">
            <h2 class="text-2xl font-bold text-slate-800">Abogados Destacados</h2>
            <span class="text-sm text-slate-500 font-medium">{{ $profiles->total() }} resultados encontrados</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($profiles as $profile)
                <div class="bg-white rounded-2xl shadow-[0_5px_20px_-5px_rgba(0,0,0,0.05)] border border-slate-100 overflow-hidden hover:shadow-[0_20px_40px_-5px_rgba(0,0,0,0.1)] hover:-translate-y-1 transition-all duration-300 flex flex-col group h-full">
                    
                    <!-- Card Header -->
                    <div class="p-6 pb-2 relative">
                        <!-- Verified Badge -->
                        @if($profile->is_verified)
                            <div class="absolute top-4 right-4 z-10" title="Verificación Oficial">
                                <div class="bg-blue-50/80 backdrop-blur-sm text-blue-600 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full flex items-center border border-blue-100 shadow-sm">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    Verificado
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-col items-center text-center">
                            <div class="relative mb-3">
                                @if($profile->profile_photo_path)
                                    <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-md group-hover:scale-105 transition-transform duration-500" src="{{ $profile->profile_photo_url }}" alt="{{ $profile->user->name }}">
                                @else
                                    <div class="h-24 w-24 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-black text-3xl border-4 border-white shadow-md group-hover:scale-105 transition-transform duration-500">
                                        {{ substr($profile->user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="absolute bottom-1 right-1 bg-green-500 w-4 h-4 rounded-full border-2 border-white" title="Disponible"></div>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 mb-1 group-hover:text-indigo-600 transition-colors">{{ $profile->user->name }}</h3>
                            <p class="text-sm font-medium text-indigo-600 mb-2">{{ $profile->headline }}</p>

                            @if($profile->city)
                                <div class="flex items-center text-xs font-semibold text-slate-500 bg-slate-50 px-3 py-1 rounded-full mb-4">
                                    <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $profile->city }}, {{ $profile->state }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Bio Snip -->
                    <div class="px-6 relative mb-4">
                        <svg class="absolute top-0 left-4 w-6 h-6 text-slate-100 transform -translate-y-2 -translate-x-1" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.0547 15.592 14.4797 17.5373 14.4797L19.98 14.4797L19.98 12.5197L17.5373 12.5197C15.592 12.5197 14.017 10.9447 14.017 8.99967L14.017 2.99967L22.983 2.99967L22.983 8.99967C22.983 10.9447 21.408 12.5197 19.4627 12.5197L19.4627 21L14.017 21ZM5.01667 21L5.01667 18C5.01667 16.0547 6.59167 14.4797 8.53733 14.4797L10.98 14.4797L10.98 12.5197L8.53733 12.5197C6.59167 12.5197 5.01667 10.9447 5.01667 8.99967L5.01667 2.99967L13.983 2.99967L13.983 8.99967C13.983 10.9447 12.408 12.5197 10.4627 12.5197L10.4627 21L5.01667 21Z"/></svg>
                        <p class="text-sm text-slate-600 line-clamp-3 pl-2 italic leading-relaxed min-h-[4.5rem]">
                            "{{ $profile->bio }}"
                        </p>
                    </div>

                    <!-- Tags -->
                    <div class="px-6 mb-6 mt-auto">
                        <div class="flex flex-wrap gap-1.5 justify-center">
                            @foreach($profile->specialties ?? [] as $tag)
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition-colors cursor-default">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Action Area -->
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex gap-3">
                        @php
                            $plan = $profile->user->tenant->plan ?? 'directory-free';
                            $isFree = $plan === 'directory-free';
                            $whatsapp = $profile->whatsapp ? preg_replace('/[^0-9]/', '', $profile->whatsapp) : null;
                            $hasContact = !empty($whatsapp);
                            $isButtonActive = !$isFree && $hasContact;
                        @endphp

                        <!-- Botón Agendar Cita -->
                        <button 
                            @if($isButtonActive)
                                wire:click="$dispatch('openBookingModal', { profileId: {{ $profile->id }} })"
                            @endif
                            class="flex-grow flex items-center justify-center gap-2 py-3 rounded-xl text-xs font-bold transition-all shadow-sm border
                            {{ $isButtonActive 
                                ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-200 active:scale-95 border-transparent cursor-pointer' 
                                : 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed' 
                            }}"
                            @if(!$isButtonActive) disabled title="{{ $isFree ? 'Esta función requiere un plan Premium' : 'El abogado no ha configurado un método de contacto' }}" @endif
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Agendar Cita</span>
                        </button>

                        <!-- Botón WhatsApp Directo -->
                        @if($whatsapp)
                            <a href="https://wa.me/{{ $whatsapp }}?text=Hola, vi su perfil en el Directorio Legal." target="_blank" class="flex-grow flex items-center justify-center gap-2 py-3 bg-green-50 border border-green-200 text-green-700 hover:bg-green-500 hover:text-white hover:border-green-500 rounded-xl transition-all text-xs font-bold" title="Enviar Mensaje por WhatsApp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                Contacto 
                            </a>
                        @endif

                        <!-- Botón Ver Perfil -->
                        <a href="{{ route('directory.show', $profile->id) }}" class="flex-shrink-0 flex items-center justify-center gap-1.5 px-3 py-3 bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 rounded-xl transition-colors text-xs font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Ver Perfil
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <div class="bg-indigo-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">No encontramos resultados</h3>
                    <p class="text-slate-500 max-w-md mx-auto">
                        Intenta buscando por otra ciudad o una palabra clave más general (ej. "Penal" en lugar de "Derecho Penal Acusatorio").
                    </p>
                    <button wire:click="$set('search', '')" class="mt-6 text-indigo-600 font-bold hover:underline">
                        Limpiar búsqueda
                    </button>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $profiles->links() }}
        </div>
    </div>

    <!-- Booking Modal Component -->
    <livewire:directory.booking-modal />
</div>
