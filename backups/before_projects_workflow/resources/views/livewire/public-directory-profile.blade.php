<div class="min-h-screen bg-gray-100"
     style="font-family: 'Inter', system-ui, sans-serif;"
     x-data="{
        shareOpen: false,
        copied: false,
        shareUrl: '{{ url()->current() }}',
        shareName: '{{ addslashes($profile->user->name) }}',
        copyLink() {
            navigator.clipboard.writeText(this.shareUrl).then(() => {
                this.copied = true;
                setTimeout(() => { this.copied = false; this.shareOpen = false; }, 2000);
            });
        }
     }">

    @php
        $whatsapp = $profile->whatsapp ? preg_replace('/[^0-9]/', '', $profile->whatsapp) : null;
        $plan = $profile->user->tenant->plan ?? 'directory-free';
        $isPremium = $plan !== 'directory-free';
        $photoUrl = $profile->profile_photo_url ?? $profile->user->profile_photo_url;
        $avatarColors = ['bg-indigo-500','bg-violet-500','bg-rose-500','bg-emerald-500','bg-amber-500'];
        $ci = abs(crc32($profile->user->name)) % count($avatarColors);
    @endphp

    <!-- ── Navbar ──────────────────────────────────────────── -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="{{ route('directory.public') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 transition-colors group">
                <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Directorio
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-gray-700 transition-colors print:hidden">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir
            </button>
        </div>
    </nav>

    <!-- ── Hero Banner ─────────────────────────────────────── -->
    <div class="w-full" style="height: 180px; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #1e40af 100%); position: relative; overflow: hidden;">
        <div style="position:absolute; inset:0; background-image: radial-gradient(rgba(255,255,255,0.12) 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div style="position:absolute; bottom:0; left:0; right:0; height:60px; background: linear-gradient(to top, #f3f4f6, transparent);"></div>
    </div>

    <!-- ── Page Content ────────────────────────────────────── -->
    <div class="max-w-5xl mx-auto px-4 pb-20 -mt-16 relative z-10">

        <!-- Profile Header Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-5 overflow-hidden">
            <div class="p-6 flex flex-col sm:flex-row gap-5 items-start">

                <!-- Avatar -->
                <div class="relative flex-shrink-0">
                    @if($profile->profile_photo_path)
                        <img src="{{ $photoUrl }}" alt="{{ $profile->user->name }}"
                             class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-4 border-white shadow-lg ring-2 ring-gray-100">
                    @else
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl {{ $avatarColors[$ci] }} flex items-center justify-center text-white text-4xl font-black border-4 border-white shadow-lg">
                            {{ strtoupper(substr($profile->user->name, 0, 1)) }}
                        </div>
                    @endif
                    <!-- Status dot -->
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-2 border-white shadow"></span>
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0 pt-1">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ $profile->user->name }}</h1>
                        @if($profile->is_verified)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Verificado
                            </span>
                        @endif
                    </div>

                    <p class="text-base font-semibold text-indigo-600 mb-3">{{ $profile->headline ?: 'Abogado' }}</p>

                    @if($profile->city)
                    <div class="flex items-center gap-1.5 text-sm text-gray-500 mb-4">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $profile->city }}, {{ $profile->state }}
                    </div>
                    @endif

                    @if($profile->whatsapp)
                    <div class="flex items-center gap-1.5 text-sm text-gray-500 mb-4">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $profile->whatsapp }}
                    </div>
                    @endif

                    <!-- Specialties -->
                    @if(!empty($profile->specialties))
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($profile->specialties as $tag)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Two-column body -->
        <div class="flex flex-col lg:flex-row gap-5 items-start">

            <!-- ── LEFT: Content ──────────────────────── -->
            <div class="flex-1 min-w-0 space-y-4">

                @if($profile->bio)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Perfil Profesional</h2>
                    <div class="flex gap-3 items-start">
                        <svg class="w-8 h-8 text-indigo-100 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                        <p class="text-gray-600 leading-relaxed text-[15px]">{{ $profile->bio }}</p>
                    </div>
                </div>
                @endif

                @if($profile->linkedin || $profile->website)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Redes y Sitio Web</h2>
                    <div class="space-y-2">
                        @if($profile->linkedin)
                        <a href="{{ $profile->linkedin }}" target="_blank"
                           class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50 transition-all group">
                            <div class="w-9 h-9 bg-blue-100 text-blue-700 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">LinkedIn</p>
                                <p class="text-xs text-gray-400 group-hover:text-blue-600 transition-colors">Ver perfil profesional →</p>
                            </div>
                        </a>
                        @endif
                        @if($profile->website)
                        <a href="{{ $profile->website }}" target="_blank"
                           class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50 transition-all group">
                            <div class="w-9 h-9 bg-indigo-100 text-indigo-700 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Sitio Web</p>
                                <p class="text-xs text-gray-400 truncate max-w-xs group-hover:text-indigo-600 transition-colors">{{ $profile->website }}</p>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

            </div>

            <!-- ── RIGHT: Sidebar ─────────────────────── -->
            <div class="w-full lg:w-64 xl:w-72 flex-shrink-0 space-y-4">

                <!-- Contact Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Contactar</p>
                    <div class="space-y-2.5">

                        @if($whatsapp)
                        <a href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hola ' . $profile->user->name . ', vi su perfil en Diogenes y quisiera contactarle.') }}"
                           target="_blank"
                           class="flex items-center justify-center gap-2 w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold text-sm shadow-sm shadow-emerald-200 active:scale-[0.98] transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            WhatsApp
                        </a>
                        @endif

                        <button
                            @if($isPremium && $whatsapp)
                                onclick="window.open('https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hola, deseo agendar una cita.') }}', '_blank')"
                            @endif
                            class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl font-semibold text-sm border transition-all active:scale-[0.98]
                            {{ ($isPremium && $whatsapp) ? 'bg-indigo-600 hover:bg-indigo-700 text-white border-transparent shadow-sm' : 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed' }}"
                            @if(!($isPremium && $whatsapp)) disabled @endif>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Agendar Cita
                        </button>

                        <!-- Compartir (Dropdown) -->
                        <div class="relative">
                            <button @click="shareOpen = !shareOpen"
                                class="relative flex items-center justify-center gap-2 w-full py-2.5 rounded-xl font-semibold text-sm border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition-all active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            Compartir perfil
                            <svg class="absolute right-3 w-3.5 h-3.5 text-indigo-400" :class="shareOpen && 'rotate-180'" style="transition:transform .2s" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                            <!-- Dropdown -->
                            <div x-show="shareOpen"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 @click.outside="shareOpen = false"
                                 style="display:none;"
                                 class="absolute bottom-full mb-2 left-0 right-0 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">

                                <div class="p-2">
                                    <!-- Copy link -->
                                    <button @click="copyLink()"
                                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 transition-colors text-left group">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors"
                                             :class="copied ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-500'">
                                            <template x-if="!copied">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </template>
                                            <template x-if="copied">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </template>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700" x-text="copied ? '¡Copiado!' : 'Copiar enlace'"></p>
                                    </button>

                                    <!-- WhatsApp share -->
                                    <a :href="'https://wa.me/?text=' + encodeURIComponent(shareName + ' – Abogado en Diogenes: ' + shareUrl)"
                                       target="_blank" @click="shareOpen = false"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 transition-colors group">
                                        <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700">WhatsApp</p>
                                    </a>

                                    <!-- X/Twitter -->
                                    <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent('¿Necesitas asesoría legal? Te recomiendo a ' + shareName) + '&url=' + encodeURIComponent(shareUrl)"
                                       target="_blank" @click="shareOpen = false"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 transition-colors group">
                                        <div class="w-8 h-8 bg-black text-white rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.259 5.622L18.243 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700">X / Twitter</p>
                                    </a>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <!-- Location -->
                @if($profile->city)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Ubicación</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">{{ $profile->city }}</p>
                            <p class="text-xs text-gray-500">{{ $profile->state }}, México</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- QR -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">QR del Perfil</p>
                    <div class="flex items-center gap-4">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&color=312e81&bgcolor=f5f3ff&data={{ urlencode(url()->current()) }}"
                             alt="QR" class="w-16 h-16 rounded-xl border border-indigo-100 flex-shrink-0">
                        <p class="text-xs text-gray-500 leading-relaxed">Escanea para acceder directamente a este perfil profesional.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="border-t border-gray-200 bg-white py-5 text-center text-xs text-gray-400 print:hidden">
        © {{ date('Y') }} Diogenes Legal · Directorio de Abogados Verificados en México
    </div>

</div>
