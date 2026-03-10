<x-slot name="header">
    <x-header title="{{ __('Explorador de Expedientes') }}" subtitle="Historial y gestión de casos jurídicos" />
</x-slot>

<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-800">Expedientes</h2>
        <a href="{{ route('expedientes.create') }}" class="w-full md:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-center font-bold">
            + Nuevo Expediente
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <input wire:model.live="search" type="text" placeholder="Buscar por número o título..." class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        
        {{-- Desktop Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Abogado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actividad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($expedientes as $exp)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                            <a href="{{ route('expedientes.show', $exp) }}" class="hover:underline">{{ $exp->numero }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $exp->titulo }}">{{ $exp->titulo }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-[150px] truncate" title="{{ $exp->cliente->nombre }}">{{ $exp->cliente->nombre }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-[150px] truncate" title="{{ $exp->abogado->name }}">{{ $exp->abogado->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $exp->estadoProcesal?->nombre ?? $exp->estado_procesal }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3 text-xs text-gray-600">
                                <span class="flex items-center" title="Actuaciones">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $exp->actuaciones_count }}
                                </span>
                                <span class="flex items-center" title="Documentos">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $exp->documentos_count }}
                                </span>
                                <span class="flex items-center" title="Eventos">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $exp->eventos_count }}
                                </span>
                                <span class="flex items-center" title="Comentarios">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    {{ $exp->comentarios_count }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('expedientes.show', $exp) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                            @can('manage users')
                                <a href="{{ route('expedientes.assignments', $exp) }}" class="text-green-600 hover:text-green-900 mr-3">Gestionar</a>
                            @endcan
                            <button wire:click="cerrar({{ $exp->id }})" wire:confirm="¿Estás seguro de cerrar este expediente?" class="text-red-600 hover:text-red-900">Cerrar</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="block md:hidden">
            @foreach($expedientes as $exp)
            <div class="p-4 border-b border-gray-200 hover:bg-gray-50 transition">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wide">{{ $exp->numero }}</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $exp->estadoProcesal?->nombre ?? $exp->estado_procesal }}
                    </span>
                </div>
                
                <div class="space-y-2 mb-4">
                    <h3 class="text-base font-bold text-gray-900 leading-tight">{{ $exp->titulo }}</h3>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="truncate">{{ $exp->cliente->nombre }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="truncate">{{ $exp->abogado->name }}</span>
                    </div>
                </div>

                {{-- Contadores móviles horizontales --}}
                <div class="flex justify-between items-center mb-4 p-3 bg-gray-50 rounded-lg text-center">
                    <div class="flex-1 border-r border-gray-200 last:border-0">
                        <div class="text-[10px] text-gray-500 uppercase font-bold">Actuaciones</div>
                        <div class="text-base font-bold text-gray-900">{{ $exp->actuaciones_count }}</div>
                    </div>
                    <div class="flex-1 border-r border-gray-200 last:border-0">
                        <div class="text-[10px] text-gray-500 uppercase font-bold">Docs</div>
                        <div class="text-base font-bold text-gray-900">{{ $exp->documentos_count }}</div>
                    </div>
                    <div class="flex-1 border-r border-gray-200 last:border-0">
                        <div class="text-[10px] text-gray-500 uppercase font-bold">Eventos</div>
                        <div class="text-base font-bold text-gray-900">{{ $exp->eventos_count }}</div>
                    </div>
                    <div class="flex-1">
                        <div class="text-[10px] text-gray-500 uppercase font-bold">Coments</div>
                        <div class="text-base font-bold text-gray-900">{{ $exp->comentarios_count }}</div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-2 border-t border-gray-100">
                    <a href="{{ route('expedientes.show', $exp) }}" class="text-indigo-600 font-bold text-sm hover:text-indigo-800 hover:underline">
                        Ver
                    </a>
                    @can('manage users')
                        <a href="{{ route('expedientes.assignments', $exp) }}" class="text-green-600 font-bold text-sm hover:text-green-800 hover:underline">
                            Gestionar
                        </a>
                    @endcan
                    <button wire:click="cerrar({{ $exp->id }})" wire:confirm="¿Estás seguro de cerrar este expediente?" class="text-red-600 font-bold text-sm hover:text-red-800 hover:underline">
                        Cerrar
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="p-4 border-t">
            {{ $expedientes->links() }}
        </div>
    </div>
</div>
