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

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-[10px] font-bold text-gray-500 uppercase">{{ $log->modulo }}</div>
                                    <div class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                        {{ strtoupper($log->accion) }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <p class="text-xs text-gray-700 leading-tight mb-1">{{ $log->descripcion }}</p>
                                    @if($log->metadatos)
                                        <button @click="alert(JSON.stringify(@js($log->metadatos), null, 2))" class="text-[9px] text-indigo-500 hover:underline font-bold">Ver JSON de Auditoría</button>
                                    @endif
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
                                <div class="flex flex-col items-end">
                                    <span class="px-2 py-0.5 inline-flex text-[9px] font-bold rounded-full bg-gray-100 text-gray-600 border border-gray-200 mb-1">
                                        {{ strtoupper($log->accion) }}
                                    </span>
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
                            
                            @if($log->metadatos)
                                <button @click="alert(JSON.stringify(@js($log->metadatos), null, 2))" class="mt-2 w-full py-1 text-[10px] text-indigo-600 font-bold border border-indigo-100 rounded bg-indigo-50/50">
                                    VER JSON DE AUDITORÍA
                                </button>
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
</div>
