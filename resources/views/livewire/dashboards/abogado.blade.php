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
            <h3 class="text-lg font-semibold">Tareas Pendientes</h3>
            <p class="text-3xl font-bold">5</p>
        </div>
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
