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
        <div class="bg-yellow-600 text-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Ingresos Mensuales</h3>
            <p class="text-3xl font-bold">$45,000 MXN</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold">Tenants Recientes</h3>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
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
        <div class="block md:hidden divide-y divide-gray-200">
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
