<div class="min-h-screen bg-gray-50/50" style="font-family: 'Inter', system-ui, sans-serif;">

    {{-- ── Page Header ──────────────────────────────────────────────── --}}
    <div class="bg-white border-b border-gray-200 mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if($profile->profile_photo_path)
                        <img src="{{ $profile->profile_photo_url }}" alt="{{ $profile->user->name }}"
                             class="w-12 h-12 rounded-xl object-cover border border-gray-200 shadow-sm">
                    @else
                        <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black text-xl shadow-sm">
                            {{ strtoupper(substr($profile->user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-black text-gray-900">{{ $profile->user->name }}</h1>
                        <div class="flex items-center gap-3 mt-0.5">
                            <span class="text-xs font-medium {{ $profile->is_public ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $profile->is_public ? '● Público' : '○ Oculto' }}
                            </span>
                            <span class="text-gray-300">·</span>
                            <span class="text-xs text-gray-400">
                                En el directorio desde {{ $profile->created_at->format('d \d\e F, Y') }}
                            </span>
                            <span class="text-gray-300">·</span>
                            @php
                                $tp = $profile->user->tenant->plan ?? '';
                                $headerPlanLabel = match(true) {
                                    str_contains($tp, 'premium') => 'Premium',
                                    str_contains($tp, 'basic')   => 'Básico',
                                    str_contains($tp, 'pro')     => 'Pro',
                                    $tp === 'trial'              => 'TRIAL (Despacho Completo - Cuenta Creada con Bug Anterior)',
                                    default                      => 'Gratuito',
                                };
                                $headerPlanClass = ($headerPlanLabel === 'Gratuito')
                                    ? 'text-gray-500 bg-gray-100'
                                    : 'text-rose-700 bg-red-100 border border-red-200';
                            @endphp
                            <span class="text-[10px] font-black {{ $headerPlanClass }} px-2 py-0.5 rounded-full">
                                {{ $headerPlanLabel === 'Gratuito' ? 'Plan ' : '' }}{{ $headerPlanLabel }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('profile.directory') }}"
                       class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar perfil
                    </a>
                    <a href="{{ route('directory.show', $profile->id) }}" target="_blank"
                       class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-indigo-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Ver mi perfil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 pb-16">

        {{-- ── Selector de período + Stats ─────────────────────────────── --}}
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-semibold text-gray-500">Mostrando estadísticas de los últimos:</p>
            <div class="flex bg-white border border-gray-200 rounded-xl p-1 gap-1 shadow-sm">
                @foreach(['7' => '7 días', '30' => '30 días', '90' => '90 días'] as $val => $label)
                    <button wire:click="$set('period', '{{ $val }}')"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all
                            {{ $period === $val ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ── Stats Cards ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @php
                $icons = [
                    'eye'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
                    'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
                    'phone'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>',
                    'share'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>',
                ];
                $bgColors = [
                    'indigo'  => 'bg-indigo-50 text-indigo-600',
                    'purple'  => 'bg-purple-50 text-purple-600',
                    'emerald' => 'bg-emerald-50 text-emerald-600',
                    'sky'     => 'bg-sky-50 text-sky-600',
                ];
            @endphp

            @foreach($stats as $key => $stat)
                @php
                    $diff    = $stat['value'] - $stat['prev'];
                    $pct     = $stat['prev'] > 0 ? round(($diff / $stat['prev']) * 100) : ($stat['value'] > 0 ? 100 : 0);
                    $isUp    = $diff >= 0;
                    $iconSvg = $icons[$stat['icon']];
                    $bgColor = $bgColors[$stat['color']];
                @endphp
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl {{ $bgColor }} flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconSvg !!}</svg>
                        </div>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ $isUp ? 'text-emerald-700 bg-emerald-50' : 'text-red-600 bg-red-50' }}">
                            {{ $isUp ? '↑' : '↓' }} {{ abs($pct) }}%
                        </span>
                    </div>
                    <p class="text-3xl font-black text-gray-900 mb-1">{{ number_format($stat['value']) }}</p>
                    <p class="text-xs text-gray-500 font-medium">{{ $stat['label'] }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">vs período anterior</p>
                </div>
            @endforeach
        </div>

        {{-- ── Main Grid ────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Chart --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Actividad del perfil</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Últimos {{ $period }} días</p>
                    </div>
                    <div class="flex items-center gap-4 text-[11px] font-semibold">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-indigo-500 inline-block"></span>Visitas</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-purple-400 inline-block"></span>Impresiones</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-emerald-400 inline-block"></span>Contactos</span>
                    </div>
                </div>

                @php
                    $maxVal     = max(1, max(array_merge($chartData['views_data'], $chartData['impressions_data'], $chartData['contacts_data'])));
                    $h          = 180;
                    $chartDates = array_slice($chartData['dates'], -15);
                    $chartViews = array_slice($chartData['views_data'], -15);
                    $chartImpr  = array_slice($chartData['impressions_data'], -15);
                    $chartCont  = array_slice($chartData['contacts_data'], -15);
                    $n          = count($chartDates);
                @endphp

                <div class="w-full overflow-x-auto">
                    <svg viewBox="0 -20 {{ max($n * 40, 400) }} {{ $h + 50 }}" class="w-full min-w-[400px]" style="height:220px">
                        @foreach([0, 0.25, 0.5, 0.75, 1] as $linePct)
                            @php $y = $h - ($linePct * $h); @endphp
                            <line x1="0" y1="{{ $y }}" x2="{{ $n * 40 }}" y2="{{ $y }}" stroke="#f1f5f9" stroke-width="1"/>
                            <text x="-4" y="{{ $y + 4 }}" text-anchor="end" font-size="9" fill="#94a3b8">{{ round($maxVal * $linePct) }}</text>
                        @endforeach
                        @foreach($chartDates as $i => $date)
                            @php
                                $x  = $i * 40 + 4;
                                $vH = ($chartViews[$i] > 0) ? max(2, ($chartViews[$i] / $maxVal) * $h) : 0;
                                $iH = ($chartImpr[$i]  > 0) ? max(2, ($chartImpr[$i]  / $maxVal) * $h) : 0;
                                $cH = ($chartCont[$i]  > 0) ? max(2, ($chartCont[$i]  / $maxVal) * $h) : 0;
                            @endphp
                            <rect x="{{ $x }}"      y="{{ $h - $vH }}" width="9" height="{{ $vH }}" fill="#6366f1" rx="2" opacity="0.9"/>
                            <rect x="{{ $x + 10 }}" y="{{ $h - $iH }}" width="9" height="{{ $iH }}" fill="#a78bfa" rx="2" opacity="0.8"/>
                            <rect x="{{ $x + 20 }}" y="{{ $h - $cH }}" width="9" height="{{ $cH }}" fill="#34d399" rx="2" opacity="0.9"/>
                            <text x="{{ $x + 12 }}" y="{{ $h + 14 }}" text-anchor="middle" font-size="8" fill="#94a3b8">{{ $date }}</text>
                        @endforeach
                    </svg>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="space-y-4">

                {{-- Porcentaje del perfil --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-gray-900">Porcentaje del perfil</h2>
                        <span class="text-2xl font-black {{ $completion['pct'] >= 80 ? 'text-emerald-600' : ($completion['pct'] >= 50 ? 'text-amber-500' : 'text-red-500') }}">
                            {{ $completion['pct'] }}%
                        </span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full mb-4 overflow-hidden">
                        <div class="h-2 rounded-full transition-all {{ $completion['pct'] >= 80 ? 'bg-emerald-500' : ($completion['pct'] >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                             style="width: {{ $completion['pct'] }}%"></div>
                    </div>
                    <div class="space-y-2">
                        @foreach($completion['fields'] as $label => $done)
                            <div class="flex items-center justify-between text-xs">
                                <span class="{{ $done ? 'text-gray-600' : 'text-gray-400' }}">{{ $label }}</span>
                                @if($done)
                                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @else
                                    <span class="w-4 h-4 rounded-full border-2 border-gray-200 inline-block"></span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($completion['pct'] < 100)
                    <a href="{{ route('profile.directory') }}"
                       class="mt-4 w-full flex items-center justify-center gap-2 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition-colors">
                        Completar perfil →
                    </a>
                    @endif
                </div>

                {{-- Info del perfil + Link --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                    <h2 class="text-sm font-bold text-gray-900 mb-4">Mi ficha pública</h2>
                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <div>
                                <p class="text-[11px] text-gray-400 font-medium">Registrado el</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $profile->created_at->format('d \d\e F \d\e Y') }}</p>
                                <p class="text-[11px] text-gray-400">hace {{ $profile->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        @php $profileUrl = route('directory.show', $profile->id); @endphp
                        <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2 border border-gray-100">
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span class="text-[11px] text-gray-500 truncate flex-1 font-mono">{{ $profileUrl }}</span>
                            <button onclick="navigator.clipboard.writeText('{{ $profileUrl }}').then(() => this.textContent = '✓').catch(() => {}); setTimeout(() => this.textContent = 'Copiar', 1500)"
                                    class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 flex-shrink-0 transition-colors">
                                Copiar
                            </button>
                        </div>

                        {{-- Ratio de conversión --}}
                        @php
                            $convRate = $totals['views'] > 0 ? round(($totals['contacts'] / $totals['views']) * 100, 1) : 0;
                        @endphp
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <div class="bg-indigo-50 rounded-xl p-3 text-center">
                                <p class="text-xl font-black text-indigo-700">{{ number_format($totals['views']) }}</p>
                                <p class="text-[10px] text-indigo-500 font-semibold">Visitas totales</p>
                            </div>
                            <div class="bg-emerald-50 rounded-xl p-3 text-center">
                                <p class="text-xl font-black text-emerald-700">{{ $convRate }}%</p>
                                <p class="text-[10px] text-emerald-500 font-semibold">Tasa conversión</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Suscripción y Pagos ──────────────────────────────────── --}}
        <div class="mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Suscripción y Pagos</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Tu plan actual en el directorio Diogenes</p>
                </div>
                @php
                    $tenant     = $profile->user->tenant;
                    $tenantPlan = $tenant->plan ?? '';
                    // Usuario de despacho = plan sin 'directory' y no vacío
                    $isDespacho = $isDespachoUser;
                    $isFreeDirPlan = !$isDespacho && (str_contains($tenantPlan, 'free') || $tenant->subscription_status !== 'active');
                    $planName   = match(true) {
                        str_contains($tenantPlan, 'premium') => 'Premium',
                        str_contains($tenantPlan, 'basic')   => 'Básico',
                        str_contains($tenantPlan, 'pro')     => 'Pro',
                        $isDespacho                          => 'Diogenes ' . ucfirst($tenantPlan),
                        default                              => 'Gratuito',
                    };
                    $badgeColor = $isDespacho
                        ? 'text-blue-700 bg-blue-50 border-blue-200'
                        : ($isFreeDirPlan ? 'text-gray-600 bg-gray-100 border-gray-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200');
                @endphp
                <span class="text-xs font-semibold border px-3 py-1 rounded-full {{ $badgeColor }}">
                    {{ ($isDespacho || !$isFreeDirPlan) ? '●' : '○' }}
                    {{ $isDespacho ? 'Despacho · ' : 'Plan ' }}{{ $planName }}
                </span>
            </div>

            {{-- Mensaje contextual según tipo de usuario --}}
            @if($payments->isEmpty())
                <div class="py-8 px-6 flex flex-col sm:flex-row items-center gap-6">

                    @if($isDespachoUser)
                        {{-- ✅ Usuario de despacho: directorio incluido en su plan --}}
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-800">El Directorio está incluido en tu plan Diogenes</p>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Tu suscripción al despacho <strong class="text-gray-700">{{ ucfirst($tenantPlan) }}</strong> incluye el acceso al directorio público sin costo adicional.
                                @if($tenant->subscription_ends_at)
                                    <br>Tu plan se renueva el <strong>{{ \Carbon\Carbon::parse($tenant->subscription_ends_at)->format('d/m/Y') }}</strong>.
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('profile.directory') }}"
                           class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-sm font-bold rounded-xl transition-colors whitespace-nowrap">
                            Completar mi perfil →
                        </a>

                    @elseif($isFreeDirPlan)
                        {{-- ⬆️ Usuario directorio-solo en plan gratuito --}}
                        <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-800">Estás en el Plan Gratuito del Directorio</p>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Actualiza a un plan de pago para aparecer primero en búsquedas, desbloquear el contacto por WhatsApp y obtener mayor visibilidad frente a clientes.
                            </p>
                        </div>
                        <a href="{{ route('billing.subscribe', 'trial') }}?context=directory"
                           class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            Ver planes del directorio →
                        </a>

                    @else
                        {{-- ✅ Usuario directorio-solo con plan activo --}}
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-800">Plan activo: {{ $planName }}</p>
                            @if($tenant->subscription_ends_at)
                                <p class="text-xs text-gray-500 mt-1">Se renueva el {{ \Carbon\Carbon::parse($tenant->subscription_ends_at)->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    @endif

                </div>

            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3 text-left">Plan</th>
                                <th class="px-6 py-3 text-left">Período</th>
                                <th class="px-6 py-3 text-left">Método</th>
                                <th class="px-6 py-3 text-right">Monto</th>
                                <th class="px-6 py-3 text-left">Estado</th>
                                <th class="px-6 py-3 text-left">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($payments as $payment)
                                @php $color = $payment->status_color; @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $payment->plan_label }}</td>
                                    <td class="px-6 py-4 text-gray-500 text-xs">
                                        @if($payment->period_start)
                                            {{ $payment->period_start->format('d/m/Y') }} – {{ $payment->period_end->format('d/m/Y') }}
                                        @else —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 capitalize">{{ $payment->method ?? '—' }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900">${{ number_format($payment->amount, 0) }} {{ $payment->currency }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100">
                                            {{ $payment->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-400 text-xs">
                                        {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y') : $payment->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Banner de Diogenes AI (Cross-sell para usuarios de directorio) --}}
            @if(!$isDespachoUser)
                <div class="px-6 pb-6 pt-2">
                    <div class="relative overflow-hidden bg-slate-900 rounded-2xl p-6 text-white group">
                        <!-- Background Glow -->
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/20 blur-[50px] rounded-full"></div>
                        <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-purple-500/10 blur-[40px] rounded-full"></div>
                        
                        <div class="relative flex flex-col md:flex-row items-center justify-between gap-6 z-10">
                            <div class="flex-1 text-center md:text-left">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase tracking-widest mb-3">
                                    Potenciado con IA
                                </span>
                                <h3 class="text-xl font-bold leading-tight">Potencia tu oficina con<br class="hidden sm:block"> Diogenes AI Integrado</h3>
                                <p class="text-xs text-slate-400 mt-2 max-w-md leading-relaxed">
                                    Control de expedientes, generación de documentos con IA, monitoreo del DOF y mucho más. 
                                    <strong>El Directorio ya está incluido en todos los planes.</strong>
                                </p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="{{ route('billing.subscribe', 'trial') }}?context=despacho" 
                                   class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-500 hover:bg-indigo-400 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-indigo-500/20 hover:-translate-y-0.5">
                                    Ver Planes Diogenes
                                </a>
                                <a href="/features" target="_blank" class="inline-flex items-center justify-center px-5 py-2.5 bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-xl text-sm font-bold transition-all">
                                    Conocer más
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
