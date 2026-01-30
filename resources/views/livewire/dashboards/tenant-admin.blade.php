<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Expedientes Activos -->
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="text-xs font-bold text-gray-500 uppercase z-10 relative">Expedientes Activos</h3>
            <p class="text-2xl font-bold text-gray-800 z-10 relative mt-1">{{ $activeExpedientes }}</p>
        </div>

        <!-- Vencimientos Próximos -->
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xs font-bold text-gray-500 uppercase z-10 relative">Vencimientos (7 días)</h3>
            <p class="text-2xl font-bold text-gray-800 z-10 relative mt-1">{{ $upcomingDeadlines }}</p>
        </div>

        @can('manage billing')
            <!-- Ingresos Mes -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-emerald-500 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xs font-bold text-gray-500 uppercase z-10 relative">Ingresos {{ now()->translatedFormat('M') }}</h3>
                <p class="text-2xl font-bold text-gray-800 z-10 relative mt-1">${{ number_format($monthlyIncome, 2) }}</p>
                <div class="flex items-center mt-2 text-xs font-medium z-10 relative">
                    @if($monthlyIncome >= $lastMonthIncome)
                        <span class="text-emerald-600 flex items-center bg-emerald-50 px-1.5 py-0.5 rounded">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            +{{ $lastMonthIncome > 0 ? round((($monthlyIncome - $lastMonthIncome) / $lastMonthIncome) * 100) : 100 }}%
                        </span>
                    @else
                        <span class="text-red-600 flex items-center bg-red-50 px-1.5 py-0.5 rounded">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                            {{ $lastMonthIncome > 0 ? round((($monthlyIncome - $lastMonthIncome) / $lastMonthIncome) * 100) : 0 }}%
                        </span>
                    @endif
                </div>
                <a href="{{ route('reportes.ingresos') }}" class="mt-3 inline-block text-[10px] font-bold text-indigo-600 hover:text-indigo-800 underline z-10 relative">Ver detalle por periodo</a>
            </div>

            <!-- Por Cobrar -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-orange-500 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xs font-bold text-gray-500 uppercase z-10 relative">Por Cobrar</h3>
                <p class="text-2xl font-bold text-gray-800 z-10 relative mt-1">${{ number_format($pendienteCobro, 2) }}</p>
                <p class="text-[10px] text-orange-600 mt-2 font-medium z-10 relative uppercase">Pendiente</p>
            </div>

            <!-- Proyección -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500 relative overflow-hidden group hover:shadow-md transition-shadow md:col-span-2">
                 <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <h3 class="text-xs font-bold text-gray-500 uppercase z-10 relative">Proy. Cierre</h3>
                <p class="text-2xl font-bold text-gray-800 z-10 relative mt-1">${{ number_format($projectedIncome, 2) }}</p>
                <p class="text-[10px] text-indigo-600 mt-2 font-medium z-10 relative uppercase">Estimado</p>
            </div>
        @else
            <!-- Card Clientes alternativa para no billing users -->
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute right-0 top-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-xs font-bold text-gray-500 uppercase z-10 relative">Total Clientes</h3>
                <p class="text-2xl font-bold text-gray-800 z-10 relative mt-1">{{ $totalClientes }}</p>
            </div>
        @endcan
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- 1. Términos Urgentes -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b flex justify-between items-center bg-red-50">
                <h3 class="text-lg font-semibold text-red-800">Términos Urgentes</h3>
                <a href="{{ route('terminos.index') }}" class="text-xs text-red-600 hover:underline font-bold">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($urgentTerminos as $termino)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-900">{{ $termino->titulo }}</p>
                                <p class="text-xs text-gray-500">Exp: {{ $termino->expediente->numero }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold {{ $termino->fecha_vencimiento->isPast() ? 'text-red-600' : 'text-orange-600' }}">
                                    {{ $termino->fecha_vencimiento->format('d/m/Y') }}
                                </p>
                                <p class="text-[10px] text-gray-400 uppercase">{{ $termino->fecha_vencimiento->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 italic">
                        No hay términos pendientes urgentes.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 2. Próximos 7 días (Agenda) -->
        @if(!auth()->user()->hasRole('super_admin'))
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b flex justify-between items-center bg-indigo-50">
                <h3 class="text-lg font-semibold text-indigo-800">Próximos 7 días</h3>
                <a href="{{ route('agenda.index') }}" class="text-xs text-indigo-600 hover:underline font-bold">Ver Agenda</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($eventos as $evento)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-100 flex flex-col items-center justify-center text-indigo-600">
                                <span class="text-[10px] font-bold uppercase">{{ $evento->start_time->format('M') }}</span>
                                <span class="text-sm font-bold">{{ $evento->start_time->format('d') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800 truncate">{{ $evento->titulo }}</p>
                                <p class="text-xs text-gray-500">{{ $evento->start_time->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 italic">
                        No hay eventos próximos.
                    </div>
                @endforelse
            </div>
        </div>
        @else
        <!-- Spacer if super admin doesn't see agenda, or handle differently -->
        <div></div> 
        @endif

        <!-- 3. Últimos Expedientes (Full Width) -->
        <div class="bg-white rounded-lg shadow overflow-hidden md:col-span-2">
            <div class="p-4 border-b">
                <h3 class="text-lg font-semibold">Últimos Expedientes</h3>
            </div>
            
            <!-- Desktop Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentExpedientes as $exp)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 text-sm font-medium text-indigo-600">
                                <a href="{{ route('expedientes.show', $exp) }}">{{ $exp->numero }}</a>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $exp->titulo }}</td>
                            <td class="px-4 py-2 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $exp->estadoProcesal?->nombre ?? $exp->estado_procesal }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm font-medium">
                                <a href="{{ route('expedientes.show', $exp) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-gray-200">
                @foreach($recentExpedientes as $exp)
                <a href="{{ route('expedientes.show', $exp) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-indigo-600 uppercase">{{ $exp->numero }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $exp->estadoProcesal?->nombre ?? $exp->estado_procesal }}
                        </span>
                    </div>
                    <h4 class="text-sm font-bold text-gray-900">{{ $exp->titulo }}</h4>
                    <div class="mt-2 flex items-center text-xs text-indigo-600 font-medium">
                        Ver detalles
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </a>
                @endforeach
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
                                <p class="text-[10px] text-gray-400">{{ $pago->fecha_pago->diffForHumans() }}</p>
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
</div>
