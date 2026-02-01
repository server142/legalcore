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
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Infraestructura Crítica</h3>
                <div class="flex items-center justify-between gap-4">
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-gray-400 uppercase font-black">Dominio / SSL</span>
                            <span class="text-sm font-bold text-gray-800 flex flex-col">
                                @if($domainDaysLeft !== null)
                                    <span>{{ $domainDaysLeft }} días <span class="text-xs font-medium text-gray-500">y {{ $domainHoursLeft }}h</span></span>
                                    @if($domainIsExpired)
                                        <span class="text-[10px] text-red-600 font-black">¡VENCIDO!</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">Sin configurar</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] text-gray-400 uppercase font-black">Costo VPS</span>
                            <span class="text-lg font-black text-indigo-600">${{ number_format($vpsCost, 2) }} <span class="text-[10px] font-normal text-gray-400 italic">USD/mes</span></span>
                        </div>
                    </div>
                    <!-- Mini Gauge Canvas -->
                    <div class="w-20 h-20 relative">
                        <canvas id="domainGauge"></canvas>
                        <div class="absolute inset-x-0 bottom-2 flex flex-col items-center justify-center">
                             <span class="text-[10px] font-black text-gray-700 leading-none">{{ $domainDaysLeft ?? 0 }}</span>
                             <span class="text-[8px] text-gray-400 uppercase font-bold">días</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center text-[10px] uppercase font-black px-1">
                <span class="text-gray-400">Vigilancia Diogenes</span>
                <span class="{{ $domainDaysLeft !== null && $domainDaysLeft < 30 ? 'text-red-500 animate-pulse' : 'text-green-500' }}">
                    {{ $domainDaysLeft < 30 ? 'Renovar Pronto' : 'Saludable' }}
                </span>
            </div>
        </div>

        <!-- Widget 2: AI Budget Trend -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Presupuesto IA</h3>
                    <p class="text-3xl font-black text-gray-900 mt-1">${{ number_format($aiCurrentSpend, 4) }} <span class="text-[10px] font-medium text-gray-400 uppercase italic">USD</span></p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-black tracking-tight text-white bg-indigo-600 px-2.5 py-1 rounded-full shadow-lg shadow-indigo-100">Límite: ${{ number_format($aiBudget, 2) }}</span>
                </div>
            </div>
            
            <!-- Area Chart for AI Trend -->
            <div class="flex-grow min-h-[80px] w-full mt-2">
                <canvas id="aiSpendTrend"></canvas>
            </div>

            <div class="mt-4 flex justify-between items-center text-[10px] text-gray-400 uppercase font-black">
                <span>Historial 30 días</span>
                <span>
                     @php $percentage = $aiBudget > 0 ? ($aiCurrentSpend / $aiBudget) * 100 : 0; @endphp
                     <span class="text-gray-900">{{ number_format($percentage, 1) }}%</span> utilizado
                </span>
            </div>
        </div>

        <!-- Widget 3: Top AI Consumers -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Consumo por Cliente</h3>
            
            <div class="flex flex-col h-full">
                @if($aiTenantUsage->count() > 0)
                    <div class="h-32 mb-4">
                        <canvas id="tenantDistribution"></canvas>
                    </div>
                    <div class="space-y-3 max-h-[120px] overflow-y-auto custom-scrollbar pr-1">
                        @foreach($aiTenantUsage as $usage)
                            <div class="flex justify-between text-[11px] items-center">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full" style="background-color: {{ ['#4f46e5', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'][$loop->index % 5] }}"></div>
                                    <span class="font-bold text-gray-700 truncate max-w-[120px]">{{ $usage->tenant->name ?? 'Sistema' }}</span>
                                </div>
                                <span class="text-gray-900 font-black tabular-nums font-mono">${{ number_format($usage->total_cost, 4) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-10 opacity-40 grayscale">
                        <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <p class="text-[10px] uppercase font-black tracking-widest">Sin datos este mes</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold">Tenants Recientes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre del Despacho</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dominio / URL</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Plan Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tenants as $tenant)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tenant->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tenant->domain }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-[10px] font-black uppercase rounded bg-indigo-50 text-indigo-700">
                                    {{ $tenant->plan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-full {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $tenant->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tenant->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let domainChart = null;
    let aiTrendChart = null;
    let tenantChart = null;

    function initDashboardCharts() {
        const domainCanvas = document.getElementById('domainGauge');
        const aiTrendCanvas = document.getElementById('aiSpendTrend');
        const tenantCanvas = document.getElementById('tenantDistribution');

        // Cleanup existing charts
        if (domainChart) domainChart.destroy();
        if (aiTrendChart) aiTrendChart.destroy();
        if (tenantChart) tenantChart.destroy();

        // 1. Gauge for Domain (Semi-circle)
        if (domainCanvas) {
            const ctxGauge = domainCanvas.getContext('2d');
            const daysLeft = {{ $domainDaysLeft ?? 0 }};
            const percentageLeft = Math.min(Math.max((daysLeft / 365) * 100, 0), 100);
            
            domainChart = new Chart(ctxGauge, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [percentageLeft, 100 - percentageLeft],
                        backgroundColor: [daysLeft < 30 ? '#ef4444' : '#4f46e5', '#f1f5f9'],
                        borderWidth: 0,
                        circumference: 180,
                        rotation: 270,
                        borderRadius: 5
                    }]
                },
                options: {
                    cutout: '80%',
                    plugins: { tooltip: { enabled: false } },
                    events: [],
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // 2. Line Chart for AI Trend
        if (aiTrendCanvas) {
            const ctxTrend = aiTrendCanvas.getContext('2d');
            let dailyData = @json($aiDailySpend->pluck('total_cost'));
            let dailyLabels = @json($aiDailySpend->pluck('date'));

            // Fallback for empty data to keep the UI beautiful
            if (dailyData.length === 0) {
                dailyData = [0, 0, 0, 0, 0];
                dailyLabels = ['', '', '', '', ''];
            }

            aiTrendChart = new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        data: dailyData,
                        borderColor: '#6366f1',
                        backgroundColor: (context) => {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradient.addColorStop(0, 'rgba(99, 102, 241, 0)');
                            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.15)');
                            return gradient;
                        },
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointBackgroundColor: '#6366f1',
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: true } },
                    scales: {
                        x: { display: false },
                        y: { 
                            display: false,
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // 3. Distribution by Tenant (Doughnut)
        if (tenantCanvas) {
            const ctxTenant = tenantCanvas.getContext('2d');
            const tenantNames = @json($aiTenantUsage->map(fn($u) => $u->tenant->name ?? 'Sistema'));
            const tenantCosts = @json($aiTenantUsage->pluck('total_cost'));

            if (tenantCosts.length > 0) {
                tenantChart = new Chart(ctxTenant, {
                    type: 'doughnut',
                    data: {
                        labels: tenantNames,
                        datasets: [{
                            data: tenantCosts,
                            backgroundColor: ['#4f46e5', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        cutout: '70%',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: { enabled: true }
                        }
                    }
                });
            }
        }
    }

    document.addEventListener('livewire:initialized', initDashboardCharts);
    document.addEventListener('livewire:navigated', initDashboardCharts);
</script>
@endpush
