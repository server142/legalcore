<div class="p-6 space-y-8">
    
    <!-- 1. Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Panel de Control</h1>
            <p class="text-sm text-slate-500 font-medium">Resumen general de tu despacho jurídico.</p>
        </div>
        <div class="flex items-center gap-3">
             <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold border border-indigo-100 flex items-center">
                <span class="w-2 h-2 rounded-full bg-indigo-500 mr-2 animate-pulse"></span>
                Operativo
            </span>
             <span class="text-xs text-slate-400 font-medium">{{ now()->translatedFormat('l, d \d\e F Y') }}</span>
        </div>
    </div>

    <!-- Billing Alert (Only for Paid Plans) -->
    @if(auth()->user()->tenant->plan !== 'trial' && auth()->user()->tenant->plan !== 'exempt' && auth()->user()->tenant->plan !== 'exento')
    <div x-data="{ 
            show: localStorage.getItem('billing_alert_w_dismissed') !== 'true' 
         }" 
         x-show="show" 
         x-transition
         class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-r shadow-sm relative">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <span class="font-bold">¡Gracias por tu confianza!</span> Para generar tus facturas fiscales deducibles, por favor completa tu <a href="{{ route('billing.profile') }}" class="font-bold underline hover:text-blue-800">Perfil de Facturación</a> lo antes posible.
                </p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="show = false; localStorage.setItem('billing_alert_w_dismissed', 'true')" type="button" class="inline-flex bg-blue-50 rounded-md p-1.5 text-blue-500 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-50 focus:ring-blue-600">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 2. KPI Cards (Modern) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- KPIs: Expedientes -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative group hover:shadow-md transition-all duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Expedientes Activos</p>
                    <h3 class="text-3xl font-black text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $activeExpedientes }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
            </div>
             <div class="mt-4 flex items-center text-xs font-medium text-emerald-600 bg-emerald-50 w-fit px-2 py-0.5 rounded-md">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                En proceso
            </div>
        </div>

        <!-- KPIs: Clientes -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative group hover:shadow-md transition-all duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Clientes</p>
                    <h3 class="text-3xl font-black text-slate-800 group-hover:text-blue-600 transition-colors">{{ $totalClientes }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-400">
                <span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md mr-1 font-bold">Activos</span> en cartera
            </div>
        </div>

        <!-- KPIs: Vencimientos -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative group hover:shadow-md transition-all duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Vencimientos (7d)</p>
                    <h3 class="text-3xl font-black {{ $upcomingDeadlines > 0 ? 'text-red-500' : 'text-slate-800' }} group-hover:text-red-600 transition-colors">{{ $upcomingDeadlines }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl {{ $upcomingDeadlines > 0 ? 'bg-red-50 text-red-600 animate-pulse' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
             <div class="mt-4 w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ min(($upcomingDeadlines * 10), 100) }}%"></div>
            </div>
        </div>

        <!-- KPIs: Finanzas -->
        @can('manage billing')
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative group hover:shadow-md transition-all duration-300">
             <div class="flex justify-between items-start">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cobranza Mes</p>
                    <h3 class="text-3xl font-black text-slate-800 group-hover:text-emerald-600 transition-colors">${{ number_format($monthlyIncome, 0) }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Por cobrar:</span>
                <span class="font-bold text-orange-500">${{ number_format($pendienteCobro, 0) }}</span>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative group hover:shadow-md transition-all duration-300 flex items-center justify-center text-center">
             <div class="space-y-2">
                 <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-xs font-bold text-slate-400">Módulo Finanzas</p>
                <p class="text-xs text-slate-300">No disponible</p>
             </div>
        </div>
        @endcan
    </div>

    <!-- 3. Main Grid (Charts & Lists) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Primary (Expedientes Recientes) takes 2/3 -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Recientes Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-50 flex justify-between items-center bg-white">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center">
                         <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-2"></span>
                        Últimos Expedientes Actualizados
                    </h3>
                    <a href="{{ route('expedientes.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">Ver todos →</a>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($recentExpedientes as $exp)
                    <div class="px-6 py-4 hover:bg-slate-50 transition-colors group cursor-pointer" onclick="window.location='{{ route('expedientes.show', $exp) }}'">
                        <div class="flex justify-between items-center">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-[10px] shrink-0">
                                    EXP
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $exp->numero }}</h4>
                                    <p class="text-xs text-slate-500 truncate max-w-[200px] sm:max-w-xs">{{ $exp->titulo }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border 
                                    {{ str_contains(strtolower($exp->estadoProcesal?->nombre ?? ''), 'conclu') ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-blue-50 text-blue-600 border-blue-100' }}">
                                    {{ $exp->estadoProcesal?->nombre ?? $exp->estado_procesal }}
                                </span>
                                <p class="text-[9px] text-slate-400 mt-1">Act: {{ $exp->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400 text-sm">
                        No hay expedientes recientes.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Gráfico Placeholder (Visual Only) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 relative overflow-hidden">
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Actividad del Despacho</h3>
                        <p class="text-xs text-slate-500 mt-1">Evolución de casos y documentos últimos 6 meses</p>
                    </div>
                     <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">+12% vs año anterior</span>
                </div>
                
                <!-- Pseudo-Chart CSS Grid -->
                <div class="h-40 flex items-end justify-between px-1 sm:px-2 gap-2 sm:gap-4">
                    @foreach($activityHistory as $month)
                    <div class="flex-1 flex items-end gap-0.5 sm:gap-1 h-full">
                        <!-- Casos Bar -->
                        <div class="flex-1 bg-indigo-500 rounded-t-sm relative group hover:bg-indigo-600 transition-colors" 
                             style="height: {{ $month['casos_h'] }}%;">
                            <div class="absolute -top-7 left-1/2 transform -translate-x-1/2 bg-slate-900 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20 shadow-xl pointer-events-none">
                                {{ $month['casos'] }} <span class="hidden sm:inline">Expedientes</span><span class="sm:hidden">Exp.</span>
                            </div>
                        </div>
                        <!-- Documentos Bar -->
                        <div class="flex-1 bg-slate-300 rounded-t-sm relative group hover:bg-slate-400 transition-colors" 
                             style="height: {{ $month['docs_h'] }}%;">
                            <div class="absolute -top-7 left-1/2 transform -translate-x-1/2 bg-slate-900 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20 shadow-xl pointer-events-none">
                                {{ $month['docs'] }} <span class="hidden sm:inline">Documentos</span><span class="sm:hidden">Docs.</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!-- X Axis -->
                <div class="border-t border-slate-100 mt-2 flex justify-between px-1 sm:px-2 pt-2 text-[9px] sm:text-[10px] text-slate-400 font-bold uppercase overflow-hidden">
                    @foreach($activityHistory as $month)
                    <span class="flex-1 text-center truncate">{{ $month['label'] }}</span>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right Column: Secondary (Agenda & Alertas) -->
        <div class="space-y-6">
            
            <!-- Agenda Widget -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                 <div class="px-5 py-4 border-b border-slate-50 flex justify-between items-center bg-white">
                    <h3 class="font-bold text-slate-800 text-sm">Agenda Semanal</h3>
                     <a href="{{ route('agenda.index') }}" class="w-6 h-6 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </a>
                </div>
                <div class="p-2">
                    @forelse($eventos as $evento)
                    <div class="flex gap-3 items-start p-3 hover:bg-slate-50 rounded-xl transition-colors mb-1">
                        <div class="flex flex-col items-center bg-slate-100 rounded-lg min-w-[45px] py-1.5 text-slate-500">
                             <span class="text-[9px] font-black uppercase tracking-tighter">{{ $evento->start_time->format('M') }}</span>
                             <span class="text-lg font-black leading-none">{{ $evento->start_time->format('d') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="text-xs font-bold text-slate-800 truncate">{{ $evento->titulo }}</h5>
                            <p class="text-[10px] text-slate-500">{{ $evento->start_time->format('H:i') }} - {{ $evento->end_time->format('H:i') }}</p>
                            @if($evento->tipo == 'audiencia')
                            <span class="inline-block mt-1 px-1.5 py-0.5 bg-red-50 text-red-600 text-[9px] font-bold rounded border border-red-100">Audiencia</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                         <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-2 text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="text-xs text-slate-400">Sin eventos esta semana</p>
                    </div>
                    @endforelse
                </div>
                <div class="bg-slate-50 p-2 text-center">
                    <a href="{{ route('agenda.index') }}" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-wider">Ver Calendario Completo</a>
                </div>
            </div>

            <!-- Términos Fatales Widget -->
            <div class="bg-gradient-to-br from-red-50 to-white rounded-2xl shadow-sm border border-red-100 p-5 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-3 opacity-10">
                     <svg class="w-20 h-20 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                
                <h3 class="text-sm font-black text-red-800 mb-4 flex items-center relative z-10">
                    <span class="w-2 h-2 rounded-full bg-red-500 mr-2 animate-pulse"></span>
                    Alertas & Términos
                </h3>

                <div class="space-y-3 relative z-10">
                    @forelse($urgentTerminos as $termino)
                    <div class="bg-white bg-opacity-80 p-3 rounded-lg border border-red-100 shadow-sm backdrop-blur-sm">
                        <div class="flex justify-between items-start">
                             <div class="flex-1">
                                <p class="text-xs font-bold text-slate-800 line-clamp-1">{{ $termino->titulo }}</p>
                                <p class="text-[10px] text-slate-500">Exp: {{ $termino->expediente->numero }}</p>
                            </div>
                            <div class="text-right pl-2">
                                <span class="text-xs font-black text-red-600 block">{{ $termino->fecha_vencimiento->format('d M') }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                     <p class="text-xs text-red-400 italic">No hay términos fatales próximos.</p>
                    @endforelse
                </div>
                
                @if($urgentTerminos->isNotEmpty())
                <div class="mt-4 text-center relative z-10">
                     <a href="{{ route('terminos.index') }}" class="text-[10px] font-bold text-red-700 hover:text-red-900 underline">Gestionar Vencimientos</a>
                </div>
                @endif
            </div>

        </div>
    </div>
    @can('manage billing')
    <div class="mt-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
            Análisis Financiero
        </h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Historico de Ingresos (Graph) -->
            <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
                <h4 class="text-xs font-bold text-gray-400 uppercase mb-6">Ingresos Últimos 6 Meses</h4>
                <div class="flex items-end justify-between h-48 space-x-2">
                    @php $maxIncome = collect($incomeHistory)->max('value') ?: 1; @endphp
                    @foreach($incomeHistory as $item)
                        <div class="flex flex-col items-center flex-1 group">
                             <div class="w-full bg-indigo-50 rounded-t-lg relative flex items-end transition-all group-hover:bg-indigo-100" style="height: 100%;">
                                 <div class="w-full bg-indigo-600 rounded-t-lg transition-all relative group-hover:bg-indigo-700" 
                                      style="height: {{ ($item['value'] / $maxIncome) * 100 }}%;">
                                      <!-- Tooltip -->
                                      <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                                          ${{ number_format($item['value']) }}
                                      </div>
                                 </div>
                             </div>
                             <span class="text-[10px] text-gray-500 mt-2 font-medium">{{ $item['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Ingresos por Materia -->
            <div class="bg-white rounded-lg shadow p-6">
                 <h4 class="text-xs font-bold text-gray-400 uppercase mb-4">Ingresos por Área (Año Actual)</h4>
                 <div class="space-y-4">
                     @php $maxMateria = collect($incomeByMateria['values'])->max() ?: 1; @endphp
                     @foreach($incomeByMateria['labels'] as $index => $label)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-medium text-gray-700">{{ $label }}</span>
                                <span class="font-bold text-gray-900">${{ number_format($incomeByMateria['values'][$index]) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($incomeByMateria['values'][$index] / $maxMateria) * 100 }}%"></div>
                            </div>
                        </div>
                     @endforeach
                     @if(empty($incomeByMateria['labels']))
                        <p class="text-xs text-gray-400 italic text-center py-4">No hay datos suficientes aún.</p>
                     @endif
                 </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Recent Payments Feed -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b bg-emerald-50">
                    <h3 class="text-sm font-bold text-emerald-800">Últimos Pagos Recibidos</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentPayments as $pago)
                        <div class="p-4 flex justify-between items-center hover:bg-gray-50">
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $pago->cliente->nombre ?? 'Cliente General' }}</p>
                                <p class="text-xs text-gray-500">
                                    @if($pago->expediente_id)
                                        Exp: <a href="{{ route('expedientes.show', $pago->expediente_id) }}" class="hover:underline text-indigo-600">{{ $pago->expediente->numero ?? 'N/A' }}</a>
                                    @else
                                        <span class="text-gray-400">Sin Expediente</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-emerald-600">+${{ number_format($pago->total, 2) }}</p>
                                <p class="text-[10px] text-gray-400">{{ optional($pago->fecha_pago)->diffForHumans() ?? 'Fecha N/A' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 text-xs">Sin pagos recientes.</div>
                    @endforelse
                </div>
            </div>

            <!-- Top Debtors -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b bg-red-50">
                    <h3 class="text-sm font-bold text-red-800">Top Deudores (Prioridad)</h3>
                </div>
                <div class="divide-y divide-gray-100">
                   @forelse($topDebtors as $deudor)
                        <div class="p-4 flex justify-between items-center hover:bg-gray-50">
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $deudor->titulo }}</p>
                                <p class="text-xs text-gray-500">{{ $deudor->cliente->nombre ?? 'Cliente' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-red-600">${{ number_format($deudor->saldo_pendiente, 2) }}</p>
                                <a href="{{ route('expedientes.show', $deudor->id) }}" class="text-[10px] text-indigo-600 hover:underline">Ver Expediente</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 text-xs">No hay deudas pendientes. ¡Excelente!</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endcan

    <!-- Monitor Inteligente (SJF / DOF) -->
    <div class="mt-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            Monitor Legal Inteligente
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('sjf.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Jurisprudencia (SJF)</p>
                        <p class="text-2xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ number_format($sjfCount) }}</p>
                        <p class="text-[10px] text-gray-500 mt-1 italic">Tesis y jurisprudencias registradas</p>
                    </div>
                    <div class="bg-indigo-50 p-3 rounded-lg text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                </div>
            </a>
            <a href="{{ route('dof.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Diario Oficial (DOF)</p>
                        <p class="text-2xl font-black text-gray-900 group-hover:text-amber-600 transition-colors">{{ number_format($dofCount) }}</p>
                        <p class="text-[10px] text-gray-500 mt-1 italic">Publicaciones oficiales monitoreadas</p>
                    </div>
                    <div class="bg-amber-50 p-3 rounded-lg text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

