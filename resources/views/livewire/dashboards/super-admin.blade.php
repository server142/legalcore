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

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold">Tenants Recientes</h3>
        </div>
        
        <!-- Desktop Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tenants as $tenant)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tenant->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tenant->slug }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tenant->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $tenant->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.tenants.index') }}" class="text-indigo-600 hover:text-indigo-900">Gestionar</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-gray-200">
            @foreach($tenants as $tenant)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">{{ $tenant->name }}</h4>
                        <p class="text-xs text-gray-500">{{ $tenant->slug }}</p>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $tenant->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $tenant->status }}
                    </span>
                </div>
                <div class="mt-3 flex justify-end">
                    <a href="{{ route('admin.tenants.index') }}" class="text-xs font-bold text-indigo-600 uppercase hover:text-indigo-800">
                        Gestionar
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
