<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-500 text-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Mis Expedientes</h3>
            <p class="text-3xl font-bold">{{ $misExpedientesCount }}</p>
        </div>
        <div class="bg-orange-500 text-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Audiencias Próximas</h3>
            <p class="text-3xl font-bold">{{ $proximasAudienciasCount }}</p>
        </div>
        <div class="bg-red-500 text-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Términos Urgentes</h3>
            <p class="text-3xl font-bold">{{ $urgentTerminos->count() }}</p>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-4 border-b flex justify-between items-center bg-red-50">
            <h3 class="text-lg font-semibold text-red-800">Términos Urgentes</h3>
            <a href="{{ route('terminos.index') }}" class="text-sm text-red-600 hover:underline">Ver todos</a>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expediente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actuación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($urgentTerminos as $termino)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">
                            {{ \Carbon\Carbon::parse($termino->fecha_vencimiento)->format('d/m/Y') }}
                            <span class="text-xs font-normal text-gray-500">({{ \Carbon\Carbon::parse($termino->fecha_vencimiento)->diffForHumans() }})</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $termino->expediente->numero }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $termino->titulo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('expedientes.show', $termino->expediente_id) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No hay términos urgentes pendientes.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="block md:hidden divide-y divide-gray-200">
            @forelse($urgentTerminos as $termino)
            <a href="{{ route('expedientes.show', $termino->expediente_id) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start mb-1">
                    <span class="text-xs font-bold text-red-600 uppercase">
                        {{ \Carbon\Carbon::parse($termino->fecha_vencimiento)->format('d/m/Y') }}
                    </span>
                    <span class="text-[10px] text-gray-500">
                        {{ \Carbon\Carbon::parse($termino->fecha_vencimiento)->diffForHumans() }}
                    </span>
                </div>
                <h4 class="text-sm font-bold text-gray-900">{{ $termino->titulo }}</h4>
                <p class="text-xs text-gray-600 mt-1">Exp: {{ $termino->expediente->numero }}</p>
                <div class="mt-2 text-xs text-indigo-600 font-medium flex items-center">
                    Ver expediente
                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
            @empty
            <div class="p-4 text-center text-sm text-gray-500">
                No hay términos urgentes pendientes.
            </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Mis Expedientes Asignados</h3>
            <a href="{{ route('expedientes.index') }}" class="text-sm text-blue-600 hover:underline">Ver todos</a>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Última Actuación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($misExpedientes as $exp)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                            <a href="{{ route('expedientes.show', $exp) }}">{{ $exp->numero }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $exp->cliente->nombre }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $exp->actuaciones()->latest()->first()?->titulo ?? 'Sin actuaciones' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('expedientes.show', $exp) }}" class="text-indigo-600 hover:text-indigo-900">Detalles</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="block md:hidden divide-y divide-gray-200">
            @foreach($misExpedientes as $exp)
            <a href="{{ route('expedientes.show', $exp) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start mb-1">
                    <span class="text-xs font-bold text-indigo-600 uppercase">{{ $exp->numero }}</span>
                </div>
                <h4 class="text-sm font-bold text-gray-900">{{ $exp->cliente->nombre }}</h4>
                <p class="text-xs text-gray-500 mt-1">
                    <span class="font-medium">Última:</span> {{ $exp->actuaciones()->latest()->first()?->titulo ?? 'Sin actuaciones' }}
                </p>
                <div class="mt-2 text-xs text-indigo-600 font-medium flex items-center">
                    Ver detalles
                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
