<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Control de Términos Legales</h2>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-4 rounded-lg shadow flex flex-wrap gap-4 items-center">
        <div class="flex-1 min-w-[200px]">
            <x-text-input wire:model.live="search" class="w-full" placeholder="Buscar por título o expediente..." />
        </div>
        <div class="w-48">
            <select wire:model.live="filtro_estado" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendientes</option>
                <option value="cumplido">Cumplidos</option>
                <option value="vencido">Vencidos</option>
            </select>
        </div>
    </div>

    <!-- Lista de Términos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expediente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Término / Acto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($terminos as $termino)
                    @php
                        $isVencido = $termino->estado === 'pendiente' && $termino->fecha_vencimiento->isPast();
                        $isProximo = $termino->estado === 'pendiente' && $termino->fecha_vencimiento->diffInDays(now()) <= 3;
                    @endphp
                    <tr class="{{ $isVencido ? 'bg-red-50' : ($isProximo ? 'bg-orange-50' : '') }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold {{ $isVencido ? 'text-red-600' : ($isProximo ? 'text-orange-600' : 'text-gray-900') }}">
                                {{ $termino->fecha_vencimiento->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $termino->fecha_vencimiento->diffForHumans() }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $termino->expediente->numero }}</div>
                            <div class="text-xs text-gray-500">{{ $termino->expediente->titulo }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium">{{ $termino->titulo }}</div>
                            <div class="text-xs text-gray-500 truncate max-w-xs">{{ $termino->descripcion }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($termino->estado === 'cumplido')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Cumplido</span>
                            @elseif($isVencido)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Vencido</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($termino->estado === 'pendiente')
                                <button wire:click="marcarComoCumplido({{ $termino->id }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-md">
                                    Marcar Cumplido
                                </button>
                            @endif
                            <a href="{{ route('expedientes.show', $termino->expediente_id) }}" class="ml-2 text-gray-600 hover:text-gray-900"> Ver Detalle</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                            No se encontraron términos que coincidan con los criterios.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $terminos->links() }}
    </div>
</div>
