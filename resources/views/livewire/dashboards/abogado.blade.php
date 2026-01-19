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
                <tr>
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

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Mis Expedientes Asignados</h3>
            <a href="#" class="text-sm text-blue-600 hover:underline">Ver todos</a>
        </div>
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
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $exp->numero }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $exp->cliente->nombre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $exp->actuaciones()->latest()->first()?->titulo ?? 'Sin actuaciones' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Detalles</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
