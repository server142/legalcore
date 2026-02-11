<x-slot name="header">
    <x-header title="{{ __('Control de Términos Legales') }}" subtitle="Seguimiento de plazos procesales" />
</x-slot>

<div class="space-y-6 mt-6">

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
                                {{ $termino->fecha_vencimiento->endOfDay()->diffForHumans() }}
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
        <div class="block md:hidden border-t border-gray-100 bg-gray-50/50 p-4 space-y-4">
            @forelse($terminos as $termino)
                @php
                    $isVencido = $termino->estado === 'pendiente' && $termino->fecha_vencimiento->isPast();
                    $isProximo = $termino->estado === 'pendiente' && $termino->fecha_vencimiento->diffInDays(now()) <= 3;
                    $statusClass = $termino->estado === 'cumplido' ? 'bg-green-100 text-green-700 border-green-200' : ($isVencido ? 'bg-red-100 text-red-700 border-red-200' : 'bg-blue-100 text-blue-700 border-blue-200');
                    $statusLabel = $termino->estado === 'cumplido' ? 'Cumplido' : ($isVencido ? 'Vencido' : 'Pendiente');
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden transition-all hover:shadow-md {{ $isVencido ? 'ring-1 ring-red-200' : ($isProximo ? 'ring-1 ring-orange-200' : '') }}">
                    <!-- Card Header -->
                    <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center {{ $isVencido ? 'bg-red-50/50' : ($isProximo ? 'bg-orange-50/50' : 'bg-gray-50/30') }}">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 rounded-lg {{ $isVencido ? 'bg-red-100 text-red-600' : ($isProximo ? 'bg-orange-100 text-orange-600' : 'bg-indigo-100 text-indigo-600') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="text-xs font-bold {{ $isVencido ? 'text-red-700' : ($isProximo ? 'text-orange-700' : 'text-gray-700') }}">
                                {{ $termino->fecha_vencimiento->format('d M, Y') }}
                            </span>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-lg border {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 space-y-3">
                        <!-- Case Info -->
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Expediente</p>
                                <p class="text-sm font-bold text-indigo-900 leading-tight">{{ $termino->expediente->numero }}</p>
                                <p class="text-[11px] text-gray-500 line-clamp-1">{{ $termino->expediente->titulo }}</p>
                            </div>
                        </div>

                        <!-- Term Info -->
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Actuación / Término</p>
                                <p class="text-sm font-extrabold text-gray-900 leading-snug">{{ $termino->titulo }}</p>
                                @if($termino->descripcion)
                                    <p class="text-xs text-gray-600 mt-1 italic">{{ $termino->descripcion }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Time Remaining -->
                        @if($termino->estado === 'pendiente')
                            <div class="mt-2 py-2 px-3 rounded-xl {{ $isVencido ? 'bg-red-50 text-red-700' : ($isProximo ? 'bg-orange-50 text-orange-700' : 'bg-blue-50 text-blue-700') }} text-center">
                                <p class="text-[10px] font-bold flex items-center justify-center gap-1.5 uppercase">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $termino->fecha_vencimiento->endOfDay()->diffForHumans() }}
                                </p>
                            </div>
                        @else
                            <div class="mt-2 py-2 px-3 rounded-xl bg-green-50 text-green-700 text-center">
                                <p class="text-[10px] font-bold flex items-center justify-center gap-1.5 uppercase">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Cumplido satisfactoriamente
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Card Actions -->
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex gap-2">
                        @if($termino->estado === 'pendiente')
                            <button wire:click="marcarComoCumplido({{ $termino->id }})" class="flex-1 py-2.5 bg-indigo-600 text-white rounded-xl text-center text-xs font-bold hover:bg-indigo-700 transition shadow-sm active:scale-95">
                                Marcar Cumplido
                            </button>
                        @endif
                        <a href="{{ route('expedientes.show', $termino->expediente_id) }}" class="{{ $termino->estado === 'pendiente' ? 'w-1/3' : 'w-full' }} py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-center text-xs font-bold hover:bg-gray-50 transition shadow-sm active:scale-95">
                            Ver Detalle
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center flex flex-col items-center gap-4">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Sin términos encontrados</p>
                        <p class="text-xs text-gray-500 mt-1 italic">Intenta ajustar los filtros de búsqueda.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        {{ $terminos->links() }}
    </div>
</div>
