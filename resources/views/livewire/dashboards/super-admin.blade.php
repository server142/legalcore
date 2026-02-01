<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-600 text-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Total Tenants</h3>
            <p class="text-3xl font-bold">{{ $totalTenants }}</p>
        </div>
        <div class="bg-green-600 text-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Tenants Activos</h3>
            <p class="text-3xl font-bold">{{ $activeTenants }}</p>
        </div>
        <div class="bg-purple-600 text-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Usuarios Totales</h3>
            <p class="text-3xl font-bold">{{ $totalUsers }}</p>
        </div>
        <div class="bg-yellow-600 text-white p-4 rounded-lg shadow relative overflow-hidden">
            <h3 class="text-lg font-semibold">Ingresos Mensuales</h3>
            <p class="text-3xl font-bold">${{ number_format($monthlyIncome, 2) }} MXN</p>
            <a href="{{ route('admin.reports.income') }}" class="text-xs underline hover:text-yellow-100 mt-2 block">Ver reporte detallado</a>
            <svg class="absolute right-0 bottom-0 w-16 h-16 text-yellow-500 opacity-20 -mb-4 -mr-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path></svg>
        </div>
    </div>

    <!-- Infrastructure & AI Monitoring -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Widget 1: Domain & VPS -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 {{ $domainDaysLeft !== null && $domainDaysLeft < 30 ? 'border-red-500' : 'border-indigo-500' }}">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Infraestructura</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Dominio / SSL</span>
                    @if($domainDaysLeft !== null)
                        <span class="px-2 py-1 rounded text-xs font-bold {{ $domainDaysLeft < 30 ? 'bg-red-100 text-red-800' : ($domainDaysLeft < 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            Vence en {{ $domainDaysLeft }} días
                        </span>
                    @else
                        <span class="text-xs text-gray-400">Sin configurar</span>
                    @endif
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Costo VPS</span>
                    <span class="font-bold text-gray-900">${{ number_format($vpsCost, 2) }} <span class="text-xs font-normal text-gray-500">USD/mes</span></span>
                </div>
            </div>
        </div>

        <!-- Widget 2: AI Budget -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Presupuesto IA Mensual</h3>
            <div class="flex justify-between items-end mb-1">
                <span class="text-2xl font-bold text-gray-800">${{ number_format($aiCurrentSpend, 2) }}</span>
                <span class="text-sm text-gray-500">de ${{ number_format($aiBudget, 2) }} USD</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                @php
                    $percentage = $aiBudget > 0 ? ($aiCurrentSpend / $aiBudget) * 100 : 0;
                    $color = $percentage > 90 ? 'bg-red-600' : ($percentage > 75 ? 'bg-yellow-400' : 'bg-green-600');
                @endphp
                <div class="{{ $color }} h-2.5 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-right">{{ number_format($percentage, 1) }}% consumido</p>
        </div>

        <!-- Widget 3: Top AI Consumers -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Top Consumo por Tenant</h3>
            <div class="overflow-y-auto max-h-32">
                <table class="w-full text-sm text-left">
                    <tbody class="divide-y divide-gray-100">
                        @forelse($aiTenantUsage as $usage)
                            <tr>
                                <td class="py-1 text-gray-700">{{ $usage->tenant->name ?? 'Sistema' }}</td>
                                <td class="py-1 text-right font-bold text-gray-900">${{ number_format($usage->total_cost, 4) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-gray-400 text-xs py-2">Sin consumo registrado este mes</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Infrastructure & AI Monitoring -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Widget 1: Domain & VPS -->
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100 flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Infraestructura</h3>
                <div class="flex items-center justify-between gap-4">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400">Dominio / SSL</span>
                            <span class="text-sm font-bold text-gray-800">
                                @if($domainDaysLeft !== null)
                                    {{ $domainDaysLeft }} días
                                @else
                                    <span class="text-gray-400">Sin fecha</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400">Costo VPS</span>
                            <span class="text-sm font-bold text-indigo-600">${{ number_format($vpsCost, 2) }} <span class="text-[10px] font-normal text-gray-400">USD/mes</span></span>
                        </div>
                    </div>
                    <!-- Mini Gauge Canvas -->
                    <div class="w-16 h-16">
                        <canvas id="domainGauge"></canvas>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center text-[10px]">
                <span class="text-gray-400">Vigilado por Diogenes</span>
                <span class="{{ $domainDaysLeft !== null && $domainDaysLeft < 30 ? 'text-red-500 font-bold' : 'text-green-500' }}">
                    {{ $domainDaysLeft < 30 ? 'Requiere Atención' : 'Estado Saludable' }}
                </span>
            </div>
        </div>

        <!-- Widget 2: AI Budget Trend -->
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Presupuesto IA Mensual</h3>
                    <p class="text-2xl font-black text-gray-900 mt-1">${{ number_format($aiCurrentSpend, 2) }} <span class="text-xs font-normal text-gray-400">USD</span></p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Límite: ${{ number_format($aiBudget, 2) }}</span>
                </div>
            </div>
            
            <!-- Area Chart for AI Trend -->
            <div class="h-20 w-full mt-2">
                <canvas id="aiSpendTrend"></canvas>
            </div>

            <div class="mt-2 flex justify-between items-center text-[10px] text-gray-400">
                <span>Últimos 30 días</span>
                <span class="font-bold text-gray-600">
                     @php $percentage = $aiBudget > 0 ? ($aiCurrentSpend / $aiBudget) * 100 : 0; @endphp
                     {{ number_format($percentage, 1) }}% usado
                </span>
            </div>
        </div>

        <!-- Widget 3: Top AI Consumers -->
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Consumo por Tenant (Proporción)</h3>
            <div class="space-y-4 max-h-40 overflow-hidden">
                @php 
                    $maxCost = $aiTenantUsage->max('total_cost') ?: 1; 
                @endphp
                @forelse($aiTenantUsage as $usage)
                    <div class="space-y-1">
                        <div class="flex justify-between text-[11px]">
                            <span class="font-bold text-gray-700 truncate max-w-[120px]">{{ $usage->tenant->name ?? 'Sistema' }}</span>
                            <span class="text-gray-900 font-black">${{ number_format($usage->total_cost, 4) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000" style="width: {{ ($usage->total_cost / $maxCost) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-4 opacity-30">
                        <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <p class="text-[10px] uppercase font-bold">Sin consumo acumulado</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold">Tenants Recientes</h3>
        </div>
        
        <!-- ... Rest of the table ... -->
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        // 1. Gauge for Domain
        const ctxGauge = document.getElementById('domainGauge').getContext('2d');
        const daysLeft = {{ $domainDaysLeft ?? 0 }};
        const percentageLeft = Math.min(Math.max((daysLeft / 365) * 100, 0), 100);
        
        new Chart(ctxGauge, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [percentageLeft, 100 - percentageLeft],
                    backgroundColor: [daysLeft < 30 ? '#ef4444' : '#4f46e5', '#f3f4f6'],
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270,
                }]
            },
            options: {
                cutout: '80%',
                plugins: { tooltip: { enabled: false } },
                events: []
            }
        });

        // 2. Line Chart for AI Trend
        const ctxTrend = document.getElementById('aiSpendTrend').getContext('2d');
        const dailyData = @json($aiDailySpend->pluck('total_cost'));
        const dailyLabels = @json($aiDailySpend->pluck('date'));

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Gasto Diario',
                    data: dailyData,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });
    });
</script>
@endpush
