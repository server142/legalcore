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
        <!-- Desktop Table -->
        <table class="min-w-full divide-y divide-gray-200 hidden md:table">
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

        <!-- Mobile Cards -->
        <div class="block md:hidden border-t border-gray-100">
            @forelse($terminos as $termino)
                @php
                    $isVencido = $termino->estado === 'pendiente' && $termino->fecha_vencimiento->isPast();
                    $isProximo = $termino->estado === 'pendiente' && $termino->fecha_vencimiento->diffInDays(now()) <= 3;
                    $statusClass = $termino->estado === 'cumplido' ? 'bg-green-100 text-green-800' : ($isVencido ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800');
                    $statusLabel = $termino->estado === 'cumplido' ? 'Cumplido' : ($isVencido ? 'Vencido' : 'Pendiente');
                @endphp
                <div class="p-5 border-b border-gray-100 hover:bg-gray-50 flex flex-col gap-3 relative transition-all {{ $isVencido ? 'border-l-4 border-l-red-500 bg-red-50/30' : ($isProximo ? 'border-l-4 border-l-orange-500 bg-orange-50/30' : '') }}">
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400 mb-0.5">Vencimiento</span>
                            <span class="text-sm font-bold {{ $isVencido ? 'text-red-600' : ($isProximo ? 'text-orange-600' : 'text-gray-900') }}">
                                {{ $termino->fecha_vencimiento->format('d/m/Y') }}
                            </span>
                            <span class="text-[10px] text-gray-500 font-medium">({{ $termino->fecha_vencimiento->diffForHumans() }})</span>
                        </div>
                        <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-xl uppercase tracking-widest {{ $statusClass }} shadow-sm">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="bg-white/60 rounded-xl p-3 border border-gray-100/50 shadow-sm">
                        <div class="flex items-center gap-2 mb-1.5 leading-none">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="text-[11px] font-bold text-gray-700 truncate">{{ $termino->expediente->numero }}</span>
                        </div>
                        <h4 class="text-sm font-extrabold text-gray-900 leading-tight mb-1">{{ $termino->titulo }}</h4>
                        <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">{{ $termino->descripcion }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 mt-1">
                        @if($termino->estado === 'pendiente')
                            <button wire:click="marcarComoCumplido({{ $termino->id }})" class="flex-1 py-2 bg-indigo-600 text-white rounded-xl text-center text-xs font-bold hover:bg-indigo-700 transition shadow-sm">
                                Marcar Cumplido
                            </button>
                        @endif
                        <a href="{{ route('expedientes.show', $termino->expediente_id) }}" class="{{ $termino->estado === 'pendiente' ? 'w-1/3' : 'w-full' }} py-2 bg-white border border-gray-200 text-gray-700 rounded-xl text-center text-xs font-bold hover:bg-gray-50 transition shadow-sm">
                            Ver Detalle
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center flex flex-col items-center gap-3">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <p class="text-sm text-gray-500 font-medium italic">No se encontraron términos que coincidan con los criterios.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        {{ $terminos->links() }}
    </div>
</div>
