<x-slot name="header">
    <x-header title="{{ __('Explorador de Expedientes') }}" subtitle="Historial y gestión de casos jurídicos" />
</x-slot>

<div class="p-4 md:p-6" x-data>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-800">Expedientes</h2>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- View Switcher -->
            <div class="bg-gray-200 p-1 rounded-lg flex gap-1">
                <button 
                    wire:click="toggleViewMode('list')" 
                    class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $viewMode === 'list' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        Lista
                    </div>
                </button>
                <button 
                    wire:click="toggleViewMode('kanban')" 
                    class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $viewMode === 'kanban' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                     <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" /></svg>
                        Tablero
                    </div>
                </button>
            </div>

            <a href="{{ route('expedientes.create') }}" class="ml-auto bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-center font-bold text-sm">
                + Nuevo
            </a>
        </div>
    </div>

    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2 flex items-center gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input wire:model.live="search" type="text" placeholder="Buscar expedientes por número, título o cliente..." class="block w-full pl-10 pr-3 py-2 border-none focus:ring-0 text-sm" />
            </div>
        </div>
    </div>

    @if($viewMode === 'list')
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
                            <span class="truncate">{{ $exp->cliente->nombre }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 pt-2 border-t border-gray-100">
                        <a href="{{ route('expedientes.show', $exp) }}" class="text-indigo-600 font-bold text-sm hover:underline">Ver</a>
                        <button wire:click="cerrar({{ $exp->id }})" wire:confirm="¿Cerrar?" class="text-red-600 font-bold text-sm hover:underline">Cerrar</button>
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
        <div class="flex overflow-x-auto pb-4 gap-4 items-start h-[calc(100vh-220px)]" id="kanban-container">
            @foreach($kanbanData as $col)
                <div 
                    wire:key="col-{{ $col['estado']->id ?? 'null' }}"
                    class="flex-shrink-0 w-80 bg-gray-100 rounded-xl flex flex-col max-h-full border border-gray-200 shadow-sm transition-colors duration-200"
                    ondragover="event.preventDefault(); this.classList.add('bg-indigo-50', 'border-indigo-300'); return false;"
                    ondragleave="this.classList.remove('bg-indigo-50', 'border-indigo-300')"
                    ondrop="this.classList.remove('bg-indigo-50', 'border-indigo-300'); handleDrop(event, {{ $col['estado']->id ?? 'null' }})"
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

                    {{-- Cards Container --}}
                    <div class="p-3 space-y-3 overflow-y-auto flex-1 custom-scrollbar">
                        @forelse($col['expedientes'] as $exp)
                            <div 
                                wire:key="card-{{ $exp->id }}"
                                draggable="true"
                                ondragstart="event.dataTransfer.setData('text/plain', '{{ $exp->id }}'); event.dataTransfer.setData('expId', '{{ $exp->id }}'); event.dataTransfer.effectAllowed = 'move'; this.classList.add('opacity-50')"
                                ondragend="this.classList.remove('opacity-50')"
                                class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm cursor-move hover:shadow-md hover:border-indigo-300 transition group relative"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded">{{ $exp->numero }}</span>
                                    {{-- Mini menu appears on hover --}}
                                    <a href="{{ route('expedientes.show', $exp) }}" title="Ver Expediente" class="text-gray-400 hover:text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </a>
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
                                    <span class="truncate" title="{{ $exp->materia }}">{{ Str::limit($exp->materia ?? 'N/A', 10) }}</span>
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
                            <div class="text-center py-6 text-gray-400 text-xs border-2 border-dashed border-gray-200 rounded-lg">
                                Sin expedientes
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <script>
            // Ensure function is global
            window.handleDrop = function(event, newStatusId) {
                event.preventDefault();
                event.stopPropagation();
                
                // Get ID from dataTransfer (support for different browser implementations)
                const expId = event.dataTransfer.getData("text/plain") || event.dataTransfer.getData("expId");
                
                if (expId) {
                    console.log('Moviendo expediente:', expId, 'a estado:', newStatusId);
                    // Call Livewire component method safely
                    @this.call('updateStatus', expId, newStatusId);
                }
            };

            // Auto-scroll functionality for drag and drop
            document.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById('kanban-container');
                if (!container) return;

                let isDragging = false;
                let scrollInterval;

                // Detect drag start/end globally or on container
                document.addEventListener('dragstart', () => { isDragging = true; });
                document.addEventListener('dragend', () => { 
                    isDragging = false; 
                    clearInterval(scrollInterval);
                });

                // Monitor drag movement
                container.addEventListener('dragover', (e) => {
                    if (!isDragging) return;

                    const threshold = 100; // Distance from edge to trigger scroll
                    const speed = 10; // Scroll speed
                    const rect = container.getBoundingClientRect();
                    const x = e.clientX;

                    clearInterval(scrollInterval);

                    // Scroll Right
                    if (x > rect.right - threshold) {
                        scrollInterval = setInterval(() => {
                            container.scrollLeft += speed;
                        }, 16);
                    }
                    // Scroll Left
                    else if (x < rect.left + threshold) {
                        scrollInterval = setInterval(() => {
                            container.scrollLeft -= speed;
                        }, 16);
                    }
                });

                container.addEventListener('dragleave', () => {
                    clearInterval(scrollInterval);
                });
            });
        </script>
        
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
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
        </style>
    @endif
</div>
