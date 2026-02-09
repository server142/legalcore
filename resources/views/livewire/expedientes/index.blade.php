<x-slot name="header">
    <x-header title="{{ __('Explorador de Expedientes') }}" subtitle="Historial y gestión de casos jurídicos" />
</x-slot>

<div class="p-4 md:p-6" x-data>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-800">Expedientes</h2>
        
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            @if(auth()->user()->can('manage expedientes') || auth()->user()->hasRole(['super_admin', 'admin']))
                <button 
                    wire:click="toggleTrash" 
                    class="flex-shrink-0 px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $showTrash ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:text-gray-900 bg-gray-100' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span class="hidden sm:inline">{{ $showTrash ? 'Salir' : 'Papelera' }}</span>
                        <span class="sm:hidden">{{ $showTrash ? 'Salir' : '' }}</span>
                    </div>
                </button>
            @endif

            <!-- View Switcher -->
            @if(!$showTrash)
            <div class="bg-gray-200 p-1 rounded-lg flex flex-shrink-0 gap-1">
                <button 
                    wire:click="toggleViewMode('list')" 
                    class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $viewMode === 'list' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        <span class="hidden sm:inline">Lista</span>
                    </div>
                </button>
                <button 
                    wire:click="toggleViewMode('kanban')" 
                    class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $viewMode === 'kanban' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                     <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" /></svg>
                        <span class="hidden sm:inline">Tablero</span>
                    </div>
                </button>
            </div>
            @endif

            <a href="{{ route('expedientes.create') }}" class="ml-auto bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-center font-bold text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Nuevo</span>
            </a>
        </div>
    </div>

    <div class="mb-6">
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input 
                wire:model.live="search" 
                type="text" 
                placeholder="Buscar expedientes por número, título o cliente..." 
                class="block w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl leading-5 placeholder-gray-400 text-gray-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition duration-150 ease-in-out shadow-sm sm:text-sm" 
            />
        </div>
    </div>

    @if($showTrash)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4 flex justify-between items-center animate-pulse">
        <span class="text-red-800 font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            PAPELERA DE RECICLAJE
        </span>
        <button wire:click="toggleTrash" class="text-sm underline text-red-600 hover:text-red-800">Salir de la papelera</button>
    </div>

    <!-- Trash Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
             <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Título</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Eliminado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-800 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expedientes as $exp)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $exp->numero }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $exp->titulo }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $exp->cliente->nombre ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 text-red-600">{{ $exp->deleted_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="restore({{ $exp->id }})" class="text-green-600 hover:text-green-900 mr-3 font-bold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                Restaurar
                            </button>
                            @if(auth()->user()->hasRole('super_admin'))
                                <button wire:click="forceDelete({{ $exp->id }})" wire:confirm="¿ELIMINAR PERMANENTEMENTE? ESTA ACCIÓN NO SE PUEDE DESHACER." class="text-red-600 hover:text-red-900 font-bold flex items-center gap-1 mt-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Eliminar Definitivamente
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">La papelera está vacía.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">
            {{ $expedientes->links() }}
        </div>
    </div>

    @elseif($viewMode === 'list')
        <div class="bg-white rounded-lg shadow overflow-hidden">
            
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
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('expedientes.show', $exp) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                                @can('manage users')
                                    <a href="{{ route('expedientes.assignments', $exp) }}" class="text-green-600 hover:text-green-900 mr-3">Gestionar</a>
                                @endcan
                                <button wire:click="cerrar({{ $exp->id }})" wire:confirm="¿Estás seguro de cerrar este expediente?" class="text-orange-600 hover:text-orange-900 mr-3">Cerrar</button>
                                @if(auth()->user()->can('manage expedientes') || auth()->user()->hasRole(['super_admin', 'admin']))
                                    <button wire:click="delete({{ $exp->id }})" wire:confirm="¿Estás seguro de ELIMINAR este expediente (Papelera)?" class="text-red-600 hover:text-red-900 font-bold" title="Eliminar/Papelera">X</button>
                                @endif
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
                            <span class="truncate">{{ $exp->cliente->nombre }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end items-center space-x-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('expedientes.show', $exp) }}" class="text-indigo-600 font-bold text-sm flex items-center gap-1 hover:underline">
                             <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Ver
                        </a>
                        
                        <button wire:click="cerrar({{ $exp->id }})" wire:confirm="¿Seguro que deseas cerrar este expediente?" class="text-orange-600 font-bold text-sm flex items-center gap-1 hover:underline">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Cerrar
                        </button>

                        @if(auth()->user()->can('manage expedientes') || auth()->user()->hasRole(['super_admin', 'admin']))
                            <button 
                                wire:click="delete({{ $exp->id }})" 
                                wire:confirm="¿Estás seguro de enviar este expediente a la papelera?"
                                class="text-red-600 font-bold text-sm flex items-center gap-1 hover:underline"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Borrar
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="p-4 border-t">
                {{ $expedientes->links() }}
            </div>
        </div>
    @else
        {{-- KANBAN BOARD VIEW --}}
        <div class="flex-row overflow-x-auto pb-4 gap-4 items-start custom-scrollbar" style="display: flex; flex-flow: row nowrap; gap: 1rem; align-items: flex-start; height: calc(100vh - 220px);" id="kanban-container">
            @foreach($kanbanData as $col)
                <div 
                    wire:key="col-{{ $col['estado']->id ?? 'null' }}"
                    class="flex-shrink-0 flex flex-col max-h-full border border-gray-200 shadow-sm bg-gray-100 rounded-xl"
                    style="min-width: 320px; width: 320px;"
                >
                    {{-- Column Header --}}
                    <div class="p-3 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl sticky top-0 z-10">
                        <h3 class="font-bold text-gray-700 text-sm truncate uppercase tracking-wider" title="{{ $col['estado']->nombre }}">
                            {{ $col['estado']->nombre }}
                        </h3>
                        <span class="bg-white border border-gray-200 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full shadow-sm">
                            {{ $col['expedientes']->count() }}
                        </span>
                    </div>

                    {{-- Cards Container (Sortable List) --}}
                    <div 
                        class="p-3 space-y-3 overflow-y-auto flex-1 custom-scrollbar kanban-list"
                        data-status-id="{{ $col['estado']->id ?? 'null' }}" 
                        style="min-height: 50px;"
                        wire:key="kanban-list-{{ $col['estado']->id ?? 'null' }}-{{ $col['expedientes']->count() }}"
                        x-init="
                            ensureSortable(() => {
                                if ($el._sortable) { $el._sortable.destroy(); }
                                $el._sortable = new Sortable($el, {
                                    group: 'expedientes',
                                    draggable: '.kanban-card',
                                    animation: 150,
                                    ghostClass: 'bg-indigo-50',
                                    dragClass: 'opacity-50',
                                    filter: 'button, a, .no-drag',
                                    preventOnFilter: false,
                                    onEnd: (evt) => {
                                        const statusId = evt.to.getAttribute('data-status-id');
                                        const ids = Array.from(evt.to.querySelectorAll('.kanban-card')).map(el => el.getAttribute('data-id'));
                                        $wire.updateOrder(statusId, ids);
                                    }
                                });
                            });
                        "
                    >
                        @forelse($col['expedientes'] as $exp)
                            <div 
                                wire:key="card-{{ $exp->id }}"
                                data-id="{{ $exp->id }}"
                                class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm cursor-move hover:shadow-md hover:border-indigo-300 transition group relative kanban-card"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded">{{ $exp->numero }}</span>
                                    
                                    {{-- Actions --}}
                                    <div class="flex items-center">
                                        <a href="{{ route('expedientes.show', $exp) }}" title="Ver Expediente" class="text-gray-400 hover:text-indigo-600 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        </a>
                                        @if(auth()->user()->can('manage expedientes') || auth()->user()->hasRole(['super_admin', 'admin']))
                                        <button 
                                            wire:click.stop="delete({{ $exp->id }})" 
                                            wire:confirm="¿Estas seguro de eliminar este expediente? Se moverá a la papelera."
                                            class="text-red-400 hover:text-red-600 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity ml-2"
                                            title="Eliminar Expediente"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                        @endif
                                    </div>
                                </div>

                                <h4 class="text-sm font-bold text-gray-900 leading-snug mb-2 line-clamp-2" title="{{ $exp->titulo }}">
                                    {{ $exp->titulo }}
                                </h4>

                                <div class="text-xs text-gray-500 space-y-1">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        <span class="truncate">{{ $exp->cliente->nombre }}</span>
                                    </div>
                                    @if($exp->abogado)
                                    <div class="flex items-center gap-1">
                                         <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        <span class="truncate">{{ $exp->abogado->name }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="mt-3 pt-2 border-t border-gray-100 grid grid-cols-3 items-center text-[10px] text-gray-400 font-medium uppercase tracking-tighter">
                                    <span class="truncate" title="{{ $exp->materia }}">{{ \Illuminate\Support\Str::limit($exp->materia ?? 'N/A', 10) }}</span>
                                    <span class="text-center" title="Última actividad: {{ $exp->updated_at->format('d/m/Y') }}">MOD: {{ $exp->updated_at->format('d/m') }}</span>
                                    <span class="text-right {{ $exp->vencimiento_termino && $exp->vencimiento_termino->isPast() ? 'text-red-600 font-black' : ($exp->vencimiento_termino && $exp->vencimiento_termino->diffInDays(now()) <= 3 ? 'text-orange-600 font-black' : '') }}">
                                        @if($exp->vencimiento_termino)
                                            <span title="Vencimiento Fatal">FATAL: {{ $exp->vencimiento_termino->format('d/m') }}</span>
                                        @else
                                            --/--
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="kanban-empty-placeholder no-drag text-center py-8 text-gray-400 text-xs border-2 border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                                <svg class="w-8 h-8 mx-auto mb-2 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                Sin expedientes
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <script>
        // Robust SortableJS Loader
        function ensureSortable(callback) {
            if (typeof Sortable !== 'undefined') {
                callback();
                return;
            }
            
            if (window._sortableLoading) {
                let checkLoaded = setInterval(() => {
                    if (typeof Sortable !== 'undefined') {
                        clearInterval(checkLoaded);
                        callback();
                    }
                }, 50);
                return;
            }

            window._sortableLoading = true;
            const script = document.createElement('script');
            script.id = 'sortable-js-cdn';
            script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js';
            script.onload = () => {
                window._sortableLoading = false;
                callback();
            };
            script.onerror = () => {
                window._sortableLoading = false;
                console.error('Failed to load SortableJS');
            };
            document.head.appendChild(script);
        }

        // Auto-scroll logic (Global)
        document.addEventListener('livewire:initialized', () => {
            const kanbanContainer = document.getElementById('kanban-container');
            if(!kanbanContainer) return;

            kanbanContainer.addEventListener('wheel', (evt) => {
                if (evt.deltaY !== 0) {
                    evt.preventDefault();
                    kanbanContainer.scrollLeft += evt.deltaY;
                }
            }, { passive: false });

            // Dragging auto-scroll
            let isDragging = false;
            let scrollInterval;

            document.addEventListener('dragstart', () => { isDragging = true; });
            document.addEventListener('dragend', () => { 
                isDragging = false; 
                if(scrollInterval) clearInterval(scrollInterval);
            });

            kanbanContainer.addEventListener('dragover', (e) => {
                if (!isDragging) return;
                const threshold = 100;
                const speed = 10;
                const rect = kanbanContainer.getBoundingClientRect();
                const x = e.clientX;

                if(scrollInterval) clearInterval(scrollInterval);

                if (x > rect.right - threshold) {
                    scrollInterval = setInterval(() => { kanbanContainer.scrollLeft += speed; }, 16);
                } else if (x < rect.left + threshold) {
                    scrollInterval = setInterval(() => { kanbanContainer.scrollLeft -= speed; }, 16);
                }
            });

            kanbanContainer.addEventListener('dragleave', () => { 
                if(scrollInterval) clearInterval(scrollInterval); 
            });
        });
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af; 
        }

        /* Hide "No cases" placeholder if the list has cards */
        .kanban-list:has(.kanban-card) .kanban-empty-placeholder {
            display: none !important;
        }
    </style>
</div>
