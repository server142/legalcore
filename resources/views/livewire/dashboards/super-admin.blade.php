<div class="p-6 bg-[#f8fafc] min-h-screen">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Panel de Control <span class="text-indigo-600">Global</span></h1>
            <p class="text-gray-500 font-medium">Monitoreo en tiempo real de la infraestructura y métricas de negocio.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="px-4 py-2 bg-white border border-gray-200 rounded-xl shadow-sm text-sm font-bold text-gray-700 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                SISTEMA OPERATIVO
             </div>
             
             <a href="{{ route('admin.database.download') }}" class="px-4 py-2 bg-red-50 border border-red-200 rounded-xl shadow-sm text-sm font-bold text-red-700 flex items-center gap-2 hover:bg-red-100 transition" onclick="return confirm('¿Estás seguro? Se solicitará tu contraseña para confirmar. Esta acción descarga una copia COMPLETA de la base de datos y es un riesgo de seguridad si el archivo cae en manos equivocadas.')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                BACKUP DB
             </a>
        </div>
    </div>

    <!-- Main Stats: Premium Floating Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-blue-700 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity"></div>
            <div class="relative bg-white border border-gray-100 rounded-3xl p-6 shadow-sm overflow-hidden min-h-[140px] flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-blue-50 p-2 rounded-xl text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em]">Total Clientes</span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900">{{ $totalTenants }}</h3>
                </div>
                <div class="text-[10px] font-bold text-gray-400 mt-4 flex items-center gap-1">
                    <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path></svg>
                    Crecimiento orgánico
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 to-teal-700 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity"></div>
            <div class="relative bg-white border border-gray-100 rounded-3xl p-6 shadow-sm overflow-hidden min-h-[140px] flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-emerald-50 p-2 rounded-xl text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em]">Suscripciones Activas</span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900">{{ $activeTenants }}</h3>
                </div>
                <div class="text-[10px] font-bold text-gray-400 mt-4">
                    Tasa de retención: <span class="text-emerald-500">98%</span>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-indigo-700 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity"></div>
            <div class="relative bg-white border border-gray-100 rounded-3xl p-6 shadow-sm overflow-hidden min-h-[140px] flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-purple-50 p-2 rounded-xl text-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <span class="text-[10px] font-black text-purple-600 uppercase tracking-[0.2em]">Usuarios Legales</span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900">{{ $totalUsers }}</h3>
                </div>
                <div class="text-[10px] font-bold text-gray-400 mt-4">
                    En red Diogenes
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-30 transition-opacity"></div>
            <div class="relative bg-white border border-gray-100 rounded-3xl p-6 shadow-sm overflow-hidden min-h-[140px] flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-amber-50 p-2 rounded-xl text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="text-[10px] font-black text-amber-600 uppercase tracking-[0.2em]">Facturación MRR</span>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900">${{ number_format($monthlyIncome, 0) }}</h3>
                </div>
                <a href="{{ route('admin.reports.income') }}" class="text-[10px] font-black text-white bg-amber-500 hover:bg-amber-600 px-3 py-1.5 rounded-full mt-4 self-start transition-colors">
                    DETALLES →
                </a>
            </div>
        </div>
    </div>

    <!-- Infrastructure & AI Monitoring: Premium Glass Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10 items-stretch">
        
        <!-- Widget 1: Health & Expiry -->
        <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col relative overflow-hidden group min-h-[480px]">
            <div class="relative z-10 flex flex-col h-full">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Infraestructura</h3>
                </div>

                <div class="space-y-8 flex-grow">
                    <div class="flex justify-between items-center bg-gray-50/50 p-6 rounded-3xl border border-gray-100">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block mb-1">Dominio / SSL</span>
                            @if($domainDaysLeft !== null)
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-black text-gray-900 tabular-nums">{{ $domainDaysLeft }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">Días</span>
                                </div>
                            @else
                                <span class="text-lg font-bold text-gray-400 uppercase">No definido</span>
                            @endif
                        </div>
                        <div class="w-20 h-20">
                            <canvas id="domainGauge"></canvas>
                        </div>
                    </div>

                    <div class="p-6 bg-white rounded-3xl border border-gray-100 flex items-center justify-between shadow-sm">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block mb-1">Costo Cloud VPS</span>
                            <span class="text-2xl font-black text-indigo-600 tabular-nums">${{ number_format($vpsCost, 2) }}</span>
                            <span class="text-[10px] text-gray-400 block font-bold mt-1">USD MENSUAL</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="px-3 py-1 bg-indigo-50 rounded-lg text-[9px] font-black text-indigo-600 mb-1">STABLE</span>
                            <span class="text-[9px] font-bold text-gray-400">DIGITAL OCEAN</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-50 flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Satus de Vigilancia</span>
                        <span class="text-xs font-bold {{ $domainDaysLeft < 30 ? 'text-red-600' : 'text-green-600' }} flex items-center gap-1.5 mt-0.5">
                            <span class="w-2 h-2 rounded-full {{ $domainDaysLeft < 30 ? 'bg-red-600 animate-pulse' : 'bg-green-600' }}"></span>
                            {{ $domainDaysLeft < 30 ? 'Acción Requerida' : 'Infraestructura Saludable' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Widget 2: AI Budget Premium (White Style Now) -->
        <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col group min-h-[480px]">
            <div class="relative z-10 flex flex-col h-full">
                <div class="flex justify-between items-start mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Inversión en IA</h3>
                    </div>
                    <span class="px-3 py-1 bg-indigo-50 rounded-full text-[9px] font-black text-indigo-600 uppercase border border-indigo-100">Límite: ${{ number_format($aiBudget, 0) }}</span>
                </div>

                <div class="mb-4">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] block mb-1">Uso Mensual Acumulado</span>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-5xl font-black text-gray-900 tabular-nums">${{ number_format($aiCurrentSpend, 4) }}</h2>
                        <span class="text-xs font-bold text-gray-400 uppercase">USD</span>
                    </div>
                </div>

                <!-- Fixed Trend Container -->
                <div class="relative flex-grow min-h-[140px] mt-4 mb-4">
                    <canvas id="aiSpendTrend"></canvas>
                </div>

                <div class="mt-auto">
                    <div class="flex justify-between text-[11px] font-black text-gray-400 uppercase mb-3">
                        <span class="flex items-center gap-2">
                             <span class="w-2 h-2 rounded-full bg-indigo-600 shadow-[0_0_8px_rgba(99,102,241,0.4)]"></span>
                             Capacidad Utilizada
                        </span>
                        @php $percentage = $aiBudget > 0 ? ($aiCurrentSpend / $aiBudget) * 100 : 0; @endphp
                        <span class="text-gray-900">{{ number_format($percentage, 2) }}%</span>
                    </div>
                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden p-[1px]">
                        <div class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-full shadow-[0_0_15px_rgba(99,102,241,0.3)] transition-all duration-1000 ease-out" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Widget 3: Data Distribution -->
        <div class="bg-white rounded-[2rem] border border-gray-100 p-8 shadow-sm flex flex-col group min-h-[480px]">
            <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-8 flex items-center gap-2">
                <span class="w-2 h-5 bg-indigo-600 rounded-full"></span>
                Uso por Despacho
            </h3>
            
            <div class="flex flex-col h-full">
                @if($aiTenantUsage->count() > 0)
                    <div class="relative h-44 mb-8 bg-gray-50/30 rounded-[2rem] flex items-center justify-center border border-gray-50">
                        <div class="w-40 h-40">
                            <canvas id="tenantDistribution"></canvas>
                        </div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-[10px] font-black text-gray-400 uppercase">Clientes</span>
                            <span class="text-2xl font-black text-gray-900 tabular-nums">{{ $aiTenantUsage->count() }}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4 max-h-[120px] overflow-y-auto custom-scrollbar pr-2 flex-grow">
                        @foreach($aiTenantUsage as $usage)
                            <div class="flex items-center justify-between group/item">
                                <div class="flex items-center gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full ring-4 ring-gray-50 shadow-sm" style="background-color: {{ ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'][$loop->index % 5] }}"></div>
                                    <span class="text-xs font-bold text-gray-700 truncate max-w-[140px] group-hover/item:text-indigo-600 transition-colors">{{ $usage->tenant->name ?? 'Sistema' }}</span>
                                </div>
                                <span class="text-xs font-black text-gray-900 tabular-nums font-mono">${{ number_format($usage->total_cost, 4) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex-grow flex flex-col items-center justify-center opacity-20 py-10 scale-90">
                        <div class="w-20 h-20 bg-gray-100 rounded-[2rem] flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        </div>
                        <p class="text-[10px] uppercase font-black tracking-[0.2em] text-center">Esperando actividad inicial</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Tenants Section: Clean Minimal Table -->
    <div class="bg-white rounded-[2rem] border border-gray-100 overflow-hidden shadow-sm">
        <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-gray-900">Despachos Recién Integrados</h3>
                <p class="text-xs font-medium text-gray-400">Últimos clientes que se unieron a la red Diogenes.</p>
            </div>
            <button class="text-xs font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-widest px-4 py-2 bg-indigo-50 rounded-xl transition-colors">Ver Todos</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.1em]">Información del Despacho</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.1em]">Dominio / Región</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.1em]">Configuración de Plan</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.1em]">Estado Global</th>
                        <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.1em]">Ingreso</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tenants as $tenant)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center font-black text-xs text-gray-400 group-hover:bg-white group-hover:shadow-sm transition-all italic">
                                        {{ substr($tenant->name, 0, 2) }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ $tenant->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-xs font-bold text-gray-500">{{ $tenant->domain ?: 'default.diogenes' }}</span>
                                <div class="text-[9px] text-gray-400 font-bold uppercase mt-0.5 tracking-tighter">SERVER: MEXICO-SFO</div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded-lg {{ $tenant->plan === 'trial' ? 'bg-orange-50 text-orange-600' : 'bg-indigo-50 text-indigo-600' }}">
                                    {{ $tenant->plan }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $tenant->status === 'active' ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]' : 'bg-gray-300' }}"></span>
                                    <span class="text-xs font-bold {{ $tenant->status === 'active' ? 'text-gray-700' : 'text-gray-400' }}">
                                        {{ $tenant->status === 'active' ? 'Completado' : 'Pendiente' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <span class="text-xs font-black text-gray-900 tabular-nums">{{ $tenant->created_at->format('d/m/Y') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
<script>
    let domainChart = null;
    let aiTrendChart = null;
    let tenantChart = null;

    function initDashboardCharts() {
        const domainCanvas = document.getElementById('domainGauge');
        const aiTrendCanvas = document.getElementById('aiSpendTrend');
        const tenantCanvas = document.getElementById('tenantDistribution');

        if (domainChart) domainChart.destroy();
        if (aiTrendChart) aiTrendChart.destroy();
        if (tenantChart) tenantChart.destroy();

        // 1. Better Domain Gauge
        if (domainCanvas) {
            const daysLeft = {{ $domainDaysLeft ?? 0 }};
            const percentageLeft = Math.min(Math.max((daysLeft / 365) * 100, 0), 100);
            domainChart = new Chart(domainCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [percentageLeft, 100 - percentageLeft],
                        backgroundColor: [daysLeft < 30 ? '#ef4444' : '#6366f1', '#f8fafc'],
                        borderWidth: 0,
                        circumference: 360,
                        rotation: 0,
                        borderRadius: 20
                    }]
                },
                options: {
                    cutout: '80%',
                    plugins: { tooltip: { enabled: false } },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // 2. High Contrast AI Spend Trend
        if (aiTrendCanvas) {
            let dailyData = @json($aiDailySpend->pluck('total_cost'));
            let dailyLabelsArr = @json($aiDailySpend->pluck('date'));
            if (dailyData.length === 0) { dailyData = [0, 0, 0, 0, 0]; dailyLabelsArr = ['', '', '', '', '']; }

            aiTrendChart = new Chart(aiTrendCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: dailyLabelsArr,
                    datasets: [{
                        data: dailyData,
                        borderColor: '#6366f1',
                        borderWidth: 4,
                        pointRadius: 0,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: (context) => {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradient.addColorStop(0, 'rgba(99, 102, 241, 0)');
                            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.2)');
                            return gradient;
                        }
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: true } },
                    scales: {
                        x: { display: false },
                        y: { display: false, beginAtZero: true }
                    }
                }
            });
        }

        // 3. Modern Tenant Distribution
        if (tenantCanvas) {
            const tenantNamesArr = @json($aiTenantUsage->map(fn($u) => $u->tenant->name ?? 'Sistema'));
            const tenantCostsArr = @json($aiTenantUsage->pluck('total_cost'));

            tenantChart = new Chart(tenantCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: tenantNamesArr,
                    datasets: [{
                        data: tenantCostsArr,
                        backgroundColor: ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'],
                        borderWidth: 4,
                        borderColor: '#ffffff',
                        hoverOffset: 10
                    }]
                },
                options: {
                    cutout: '75%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: true } }
                }
            });
        }
    }

    document.addEventListener('livewire:initialized', initDashboardCharts);
    document.addEventListener('livewire:navigated', initDashboardCharts);
</script>
@endpush
