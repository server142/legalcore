<x-slot name="header">
    <x-header title="{{ __('Bitácora de Actividades') }}" subtitle="Auditoría de acciones en el sistema" />
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <!-- Barra de Filtros -->
            <!-- Barra de Filtros -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="relative">
                    <input wire:model.live="search" type="text" placeholder="Buscar texto..." class="w-full rounded-lg border-gray-300 pl-10 focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                @if($isSuperAdmin)
                <div>
                    <select wire:model.live="filterTenant" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Todos los Despachos</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <select wire:model.live="filterModule" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Todos los Módulos</option>
                        @foreach($modules as $m)
                            <option value="{{ $m }}">{{ ucfirst($m) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select wire:model.live="filterAction" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Todas las Acciones</option>
                        <option value="create">Creación</option>
                        <option value="update">Actualización</option>
                        <option value="delete">Eliminación</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                        <option value="login_fallido">Intento Fallido</option>
                    </select>
                </div>

                <div>
                    <select wire:model.live="filterSeverity" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Todas las Severidades</option>
                        <option value="low">Baja (Info)</option>
                        <option value="medium">Media (Advertencia)</option>
                        <option value="critical">Crítica (Peligro)</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <!-- Desktop Table -->
                <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                    <thead class="bg-gray-50 text-[10px] uppercase font-bold text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Nivel</th>
                            <th class="px-6 py-3 text-left">Fecha / Hora</th>
                            @if($isSuperAdmin)
                                <th class="px-6 py-3 text-left">Despacho</th>
                            @endif
                            <th class="px-6 py-3 text-left">Usuario</th>
                            <th class="px-6 py-3 text-left">Módulo / Acción</th>
                            <th class="px-6 py-3 text-left">Descripción / Auditoría</th>
                            <th class="px-6 py-3 text-left">Sistema / IP</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 transition border-l-4 
                                {{ $log->severity == 'critical' ? 'border-red-500 bg-red-50/30' : 
                                   ($log->severity == 'medium' ? 'border-orange-400 bg-orange-50/20' : 'border-emerald-400') }}">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($log->severity == 'critical')
                                            <span class="flex h-2 w-2 rounded-full bg-red-500 animate-pulse mr-2"></span>
                                        @elseif($log->severity == 'medium')
                                            <span class="flex h-2 w-2 rounded-full bg-orange-400 mr-2"></span>
                                        @else
                                            <span class="flex h-2 w-2 rounded-full bg-emerald-400 mr-2"></span>
                                        @endif
                                        <span class="text-[10px] font-bold uppercase {{ $log->severity == 'critical' ? 'text-red-700' : ($log->severity == 'medium' ? 'text-orange-700' : 'text-emerald-700') }}">
                                            {{ $log->severity }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs text-gray-900 font-medium">{{ $log->created_at->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>

                                @if($isSuperAdmin)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-[10px] font-bold text-indigo-600 truncate max-w-[100px]" title="{{ $log->tenant->name ?? 'N/A' }}">
                                            {{ $log->tenant->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                @endif

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-7 w-7 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-[10px] mr-2">
                                            {{ substr($log->user->name ?? 'S', 0, 1) }}
                                        </div>
                                        <div class="text-xs font-semibold text-gray-800">{{ $log->user->name ?? 'Sistema' }}</div>
                                    </div>
                                </td>

                                @php
                                    $action = strtolower($log->accion);
                                    $style = match(true) {
                                        str_contains($action, 'create') || str_contains($action, 'crear') => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                                        str_contains($action, 'update') || str_contains($action, 'actualizar') => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                                        str_contains($action, 'delete') || str_contains($action, 'elimin') || str_contains($action, 'borrar') => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'dot' => 'bg-rose-500'],
                                        str_contains($action, 'login') && !str_contains($action, 'fail') && !str_contains($action, 'fall') => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'dot' => 'bg-indigo-500'],
                                        str_contains($action, 'logout') => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'dot' => 'bg-slate-500'],
                                        str_contains($action, 'fail') || str_contains($action, 'fall') => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500'],
                                        default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400'],
                                    };
                                @endphp
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">{{ $log->modulo }}</div>
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }} shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }} mr-1.5"></span>
                                        {{ strtoupper($log->accion) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-gray-700 leading-tight mb-1">{{ $log->descripcion }}</p>
                                    <div class="flex space-x-2">
                                        @if($log->metadatos)
                                            <button @click="alert(JSON.stringify(@js($log->metadatos), null, 2))" class="text-[9px] text-indigo-500 hover:underline font-bold">Ver JSON</button>
                                        @endif
                                        <button wire:click="showDetails({{ $log->id }})" class="text-[9px] text-emerald-600 hover:underline font-bold">Detalles Avanzados</button>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="flex items-center text-[10px] text-gray-600">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            {{ $log->browser ?: 'Browser' }} / {{ $log->os ?: 'OS' }}
                                        </div>
                                        <div class="flex items-center text-[10px] font-mono text-indigo-500 mt-1">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 3a10.003 10.003 0 00-6.212 16.883L12 21l6.212-4.117A10.003 10.003 0 0012 3"></path></svg>
                                            {{ $log->ip_address }}
                                        </div>
                                        @if($log->session_id)
                                            <div class="text-[8px] text-gray-400 mt-1 font-mono">SESS: {{ substr($log->session_id, 0, 8) }}...</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No se encontraron registros en la bitácora.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Mobile Cards -->
                <div class="block md:hidden">
                    @forelse($logs as $log)
                        <div class="p-4 border-b border-gray-200 hover:bg-gray-50 transition border-l-4 
                            {{ $log->severity == 'critical' ? 'border-red-500' : ($log->severity == 'medium' ? 'border-orange-400' : 'border-emerald-400') }}">
                            
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs mr-2">
                                        {{ substr($log->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $log->user->name ?? 'Sistema' }}</div>
                                        @if($isSuperAdmin)
                                            <div class="text-[10px] font-bold text-indigo-600 uppercase">{{ $log->tenant->name ?? 'N/A' }}</div>
                                        @endif
                                        <div class="text-[10px] text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                                @php
                                    $action = strtolower($log->accion);
                                    $style = match(true) {
                                        str_contains($action, 'create') || str_contains($action, 'crear') => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                                        str_contains($action, 'update') || str_contains($action, 'actualizar') => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                                        str_contains($action, 'delete') || str_contains($action, 'elimin') || str_contains($action, 'borrar') => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'dot' => 'bg-rose-500'],
                                        str_contains($action, 'login') && !str_contains($action, 'fail') && !str_contains($action, 'fall') => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'dot' => 'bg-indigo-500'],
                                        str_contains($action, 'logout') => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'dot' => 'bg-slate-500'],
                                        str_contains($action, 'fail') || str_contains($action, 'fall') => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500'],
                                        default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400'],
                                    };
                                @endphp
                                <div class="flex flex-col items-end">
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold border {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }} mb-1">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }} mr-1.5"></span>
                                        {{ strtoupper($log->accion) }}
                                    </div>
                                    <span class="text-[9px] font-extrabold uppercase {{ $log->severity == 'critical' ? 'text-red-600 animate-pulse' : ($log->severity == 'medium' ? 'text-orange-600' : 'text-emerald-600') }}">
                                        {{ $log->severity }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-2">
                                <p class="text-xs text-gray-800">{{ $log->descripcion }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-[10px] text-gray-500 mt-2 bg-gray-50 p-2 rounded-lg">
                                <div class="flex items-center">
                                    <span class="font-bold mr-1">MÓDULO:</span> {{ ucfirst($log->modulo) }}
                                </div>
                                <div class="flex items-center">
                                    <span class="font-bold mr-1">IP:</span> {{ $log->ip_address }}
                                </div>
                                <div class="flex items-center col-span-2">
                                    <span class="font-bold mr-1">SISTEMA:</span> {{ $log->browser ?: 'N/A' }} ({{ $log->os ?: 'N/A' }})
                                </div>
                            </div>
                            
                            @if($log->metadatos || true)
                                <div class="flex space-x-2 mt-2">
                                    <button wire:click="showDetails({{ $log->id }})" class="flex-1 py-1.5 text-[10px] text-white font-bold rounded bg-emerald-600 shadow-sm">
                                        DETALLES COMPLETOS
                                    </button>
                                    @if($log->metadatos)
                                        <button @click="alert(JSON.stringify(@js($log->metadatos), null, 2))" class="flex-1 py-1.5 text-[10px] text-indigo-600 font-bold border border-indigo-100 rounded bg-indigo-50/50">
                                            JSON
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 italic text-sm">
                            No se encontraron registros en la bitácora.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    <!-- Modal de Detalles -->
    @if($selectedLog)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeDetails"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border-l-8 {{ $selectedLog->severity == 'critical' ? 'border-red-600' : ($selectedLog->severity == 'medium' ? 'border-orange-500' : 'border-emerald-500') }}">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-left w-full">
                            <div class="flex justify-between items-center mb-4 pb-2 border-b">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 flex items-center" id="modal-title">
                                    Detalle de Auditoría #{{ $selectedLog->id }}
                                    <span class="ml-3 px-2 py-0.5 rounded-full text-[10px] uppercase font-black {{ $selectedLog->severity == 'critical' ? 'bg-red-100 text-red-800' : ($selectedLog->severity == 'medium' ? 'bg-orange-100 text-orange-800' : 'bg-emerald-100 text-emerald-800') }}">
                                        {{ $selectedLog->severity }}
                                    </span>
                                </h3>
                                <button wire:click="closeDetails" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Módulo / Acción</label>
                                        <div class="text-sm font-semibold text-gray-800">{{ ucfirst($selectedLog->modulo) }} » {{ strtoupper($selectedLog->accion) }}</div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Fecha y Hora</label>
                                        <div class="text-sm text-gray-800">{{ $selectedLog->created_at->format('d/m/Y H:i:s') }} ({{ $selectedLog->created_at->diffForHumans() }})</div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ejecutado por</label>
                                        <div class="flex items-center mt-1">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-black text-xs mr-2">
                                                {{ substr($selectedLog->user->name ?? 'S', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $selectedLog->user->name ?? 'Sistema (Automático)' }}</div>
                                                <div class="text-[10px] text-gray-500">{{ $selectedLog->user->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($isSuperAdmin)
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Despacho / Tenant</label>
                                        <div class="text-sm font-bold text-indigo-600">{{ $selectedLog->tenant->name ?? 'N/A' }}</div>
                                    </div>
                                    @endif
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dirección IP</label>
                                        <div class="text-sm font-mono text-gray-800">{{ $selectedLog->ip_address }}</div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Identificador de Sesión</label>
                                        <div class="text-sm font-mono text-gray-500 truncate" title="{{ $selectedLog->session_id }}">{{ $selectedLog->session_id }}</div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Navegador y Sistema</label>
                                        <div class="text-sm text-gray-800 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            {{ $selectedLog->browser }} / {{ $selectedLog->os }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">User Agent Original</label>
                                        <div class="text-[9px] text-gray-500 leading-tight border p-1 rounded bg-gray-50 font-mono break-all">
                                            {{ $selectedLog->user_agent }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-gray-50 rounded-lg border">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Descripción de la Actividad</label>
                                <div class="text-sm text-gray-800 leading-relaxed italic">"{{ $selectedLog->descripcion }}"</div>
                            </div>

                            @if($selectedLog->metadatos)
                            <div class="mt-4">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Metadatos de Seguimiento (JSON)</label>
                                <pre class="text-[10px] bg-slate-900 text-emerald-400 p-4 rounded-lg overflow-x-auto font-mono max-h-48">{{ json_encode($selectedLog->metadatos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="closeDetails" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Cerrar Detalle
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
