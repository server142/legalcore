<x-slot name="header">
    <x-header title="{{ __('Control de Asesorías') }}" subtitle="Prospectos y primeras atenciones" />
</x-slot>

<div class="p-4 md:p-6 space-y-6">
    {{-- Notificaciones Flash --}}
    @if (session()->has('message'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm mb-4" role="alert">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <p class="font-bold">{{ session('message') }}</p>
            </div>
        </div>
        <script>
            setTimeout(function() {
                const alert = document.querySelector('[role="alert"]');
                if (alert) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 4000);
        </script>
    @endif

    @if (session()->has('facturaUrl'))
        <div class="p-4 bg-indigo-50 border-l-4 border-indigo-500 text-indigo-700 rounded-r-xl shadow-sm mb-4 flex justify-between items-center">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3h7l3 3v15a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                <p class="font-bold">El recibo de pago está listo para descargar.</p>
            </div>
            <a href="{{ session('facturaUrl') }}" target="_blank" class="px-4 py-1 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition">Descargar PDF</a>
        </div>
    @endif

    {{-- Header y Botón Nuevo --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Control de Asesorías</h2>
            <p class="text-sm text-gray-500">Gestiona citas, seguimientos y conversiones a clientes</p>
        </div>
            <div class="flex items-center gap-3">
                <button type="button" wire:click="openCampaignModal" class="inline-flex items-center justify-center p-2.5 bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-100 transition shadow-sm border border-indigo-100" title="Configurar Landings de Campaña">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                </button>
                <button type="button" wire:click="$dispatch('open-modal', 'modal-asesoria')" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 whitespace-nowrap">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>Nueva Asesoría</span>
                </button>
            </div>
    </div>

    {{-- Tarjetas de Estadísticas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Para Hoy</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['hoy'] }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="p-3 bg-orange-100 text-orange-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pendientes</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['pendientes'] }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="p-3 bg-green-100 text-green-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Realizadas (Mes)</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['realizadas_mes'] }}</p>
            </div>
        </div>
        <div class="bg-indigo-50 p-4 rounded-xl shadow-sm border border-indigo-100 flex items-center space-x-4">
            <div class="p-3 bg-indigo-600 text-white rounded-lg shadow-lg shadow-indigo-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
            </div>
            <div>
                <p class="text-sm text-indigo-700 font-bold uppercase tracking-tighter">Campaña Abril</p>
                <p class="text-2xl font-black text-indigo-900">{{ $stats['campania_total'] }}</p>
            </div>
        </div>
    </div>

    {{-- Filtros y Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input wire:model.live="search" type="text" placeholder="Buscar por nombre, folio o asunto..." class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="flex gap-2 overflow-x-auto pb-2 md:pb-0">
                <select wire:model.live="filtroEstado" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Todos los estados</option>
                    <option value="agendada">Agendada</option>
                    <option value="realizada">Realizada</option>
                    <option value="cancelada">Cancelada</option>
                    <option value="no_atendida">No Atendida</option>
                </select>
                <select wire:model.live="filtroTipo" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Todos los tipos</option>
                    <option value="presencial">Presencial</option>
                    <option value="telefonica">Telefónica</option>
                    <option value="videoconferencia">Videoconferencia</option>
                </select>
                <select wire:model.live="filtroFecha" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Cualquier fecha</option>
                    <option value="hoy">Hoy</option>
                    <option value="semana">Esta Semana</option>
                    <option value="mes">Este Mes</option>
                </select>
                <div class="flex items-center ml-2 border-l border-gray-200 pl-4 py-1">
                    <select wire:model.live="filtroOrigen" class="bg-gray-50 border-0 text-gray-700 text-xs font-bold rounded-lg focus:ring-2 focus:ring-indigo-600 px-2 py-1.5 cursor-pointer">
                        <option value="">✨ Todos los Orígenes</option>
                        @foreach($origenesDisponibles as $origen)
                            <option value="{{ $origen }}">{{ $origen }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent">
            <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Folio / Prospecto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Abogado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($asesorias as $asesoria)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-indigo-600">{{ $asesoria->folio }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-900">{{ $asesoria->nombre_prospecto }}</span>
                                        @if(preg_match('/\[ORIGEN: (.*?)\]/', $asesoria->notas, $matches))
                                            <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-black bg-indigo-600 text-white uppercase tracking-tighter" title="{{ $matches[1] }}">
                                                {{ Str::limit($matches[1], 12) }}
                                            </span>
                                        @elseif(str_contains($asesoria->notas, 'LANDING CAMPAÑA ABRIL 2026'))
                                            <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-black bg-indigo-600 text-white uppercase tracking-tighter">
                                                Campaña Abril
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 truncate max-w-[200px]">{{ $asesoria->asunto }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">{{ $asesoria->fecha_hora->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $asesoria->fecha_hora->format('H:i') }} ({{ $asesoria->duracion_minutos }} min)</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($asesoria->tipo == 'presencial')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        🏢 Presencial
                                    </span>
                                @elseif($asesoria->tipo == 'telefonica')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        📞 Telefónica
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        📹 Video
                                    </span>

                                    @if(!empty($asesoria->link_videoconferencia))
                                        <div class="mt-1">
                                            <a href="{{ $asesoria->link_videoconferencia }}" target="_blank" rel="noopener noreferrer" class="text-xs text-indigo-600 underline hover:text-indigo-800 font-bold">
                                                Abrir videollamada
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($asesoria->estado == 'agendada')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Agendada</span>
                                @elseif($asesoria->estado == 'realizada')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Realizada</span>
                                @elseif($asesoria->estado == 'cancelada')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelada</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">No Atendida</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $asesoria->abogado->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-end gap-1">
                                    @if($isAdmin || $asesoria->abogado_id == $currentUserId)
                                        <button type="button" wire:click="edit({{ $asesoria->id }})" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 transition" title="Editar asesoría">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button type="button" wire:click="delete({{ $asesoria->id }})" wire:confirm="¿Estás seguro de eliminar esta asesoría?" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition" title="Eliminar asesoría">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    <button type="button" wire:click="compartirTarjeta({{ $asesoria->id }})" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition" title="Ver comprobante de cita">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 7h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z" />
                                        </svg>
                                    </button>
                                    @if(!empty($asesoria->telefono))
                                        <button type="button" wire:click="compartirTarjetaWhatsApp({{ $asesoria->id }})" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 transition" title="WhatsApp">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 20l1.5-4.5A8.5 8.5 0 1112 20a8.4 8.4 0 01-3.6-.8L3 20z" />
                                            </svg>
                                        </button>
                                    @endif
                                    @if($canManageBilling && $asesoriasBillingEnabled && $asesoria->pagado)
                                         @if($asesoria->factura_id)
                                            <a href="{{ route('reportes.factura', $asesoria->factura_id) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition" title="Recibo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3h7l3 3v15a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                                            </a>
                                         @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <p class="mt-2 text-sm font-medium">No se encontraron asesorías</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="block md:hidden">
            @forelse($asesorias as $asesoria)
                <div class="p-4 border-b border-gray-200 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-wide">{{ $asesoria->folio }}</span>
                        @if($asesoria->estado == 'agendada')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Agendada</span>
                        @elseif($asesoria->estado == 'realizada')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Realizada</span>
                        @elseif($asesoria->estado == 'cancelada')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelada</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No Atendida</span>
                        @endif
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-gray-900 leading-tight">{{ $asesoria->nombre_prospecto }}</h3>
                            @if(preg_match('/\[ORIGEN: (.*?)\]/', $asesoria->notas, $matches))
                                <span class="px-2 py-0.5 rounded text-[8px] font-black bg-indigo-600 text-white uppercase">{{ $matches[1] }}</span>
                            @elseif(str_contains($asesoria->notas, 'LANDING CAMPAÑA ABRIL 2026'))
                                <span class="px-2 py-0.5 rounded text-[8px] font-black bg-indigo-600 text-white uppercase">Campaña Abril</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 truncate">{{ $asesoria->asunto }}</div>

                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $asesoria->fecha_hora->format('d/m/Y H:i') }} ({{ $asesoria->duracion_minutos }} min)</span>
                        </div>

                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="truncate">{{ $asesoria->abogado->name }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($asesoria->tipo == 'presencial')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">🏢 Presencial</span>
                            @elseif($asesoria->tipo == 'telefonica')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">📞 Telefónica</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">📹 Video</span>
                                @if(!empty($asesoria->link_videoconferencia))
                                    <a href="{{ $asesoria->link_videoconferencia }}" target="_blank" rel="noopener noreferrer" class="text-xs text-indigo-600 underline hover:text-indigo-800 font-bold">Abrir videollamada</a>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2 border-t border-gray-100">
                        @if($isAdmin || $asesoria->abogado_id == $currentUserId)
                            <button type="button" wire:click="edit({{ $asesoria->id }})" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-900" title="Editar asesoría">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button type="button" wire:click="delete({{ $asesoria->id }})" wire:confirm="¿Estás seguro de eliminar esta asesoría?" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 hover:text-red-900" title="Eliminar asesoría">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        @endif
                        
                        <button type="button" wire:click="compartirTarjeta({{ $asesoria->id }})" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 hover:text-indigo-900" title="Ver comprobante de cita">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 7h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z" />
                            </svg>
                        </button>

                        @if(!empty($asesoria->telefono))
                            <button type="button" wire:click="compartirTarjetaWhatsApp({{ $asesoria->id }})" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 hover:text-green-900" title="Enviar comprobante de cita por WhatsApp">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 20l1.5-4.5A8.5 8.5 0 1112 20a8.4 8.4 0 01-3.6-.8L3 20z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.5 9.8c.2-.7.6-1.2 1.1-1.4.4-.2 1-.1 1.4.2.6.5 1.2 1.2 1.7 1.8.3.4.4 1 .2 1.4-.2.5-.7.9-1.4 1.1.7 1.1 1.6 2 2.7 2.7.2-.7.6-1.2 1.1-1.4.4-.2 1-.1 1.4.2.6.5 1.2 1.2 1.7 1.8.3.4.4 1 .2 1.4-.4.8-1.2 1.3-2.4 1.4-1.5.2-3.6-.7-5.6-2.7-2-2-2.9-4.1-2.7-5.6.1-1.2.6-2 1.4-2.4z" />
                                </svg>
                            </button>
                        @endif

                        @if($canManageBilling && $asesoriasBillingEnabled)
                            @if($asesoria->pagado)
                                @if($asesoria->factura_id)
                                    <a href="{{ route('reportes.factura', $asesoria->factura_id) }}" target="_blank" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900" title="Abrir recibo (PDF)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3h7l3 3v15a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3v4h4" />
                                        </svg>
                                    </a>
                                @else
                                    <button type="button" wire:click="generarRecibo({{ $asesoria->id }})" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:text-indigo-800" title="Generar recibo (PDF)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3h7l3 3v15a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3v4h4" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v6m-3-3h6" />
                                        </svg>
                                    </button>
                                @endif

                                @if(!empty($asesoria->telefono))
                                    @php
                                        $phone = preg_replace('/\D+/', '', $asesoria->telefono);
                                        $msg = "Hola, te comparto el recibo de tu asesoría ({$asesoria->folio}).";
                                        if ($asesoria->factura_id) {
                                            $msg .= " Puedes descargarlo aquí: " . route('reportes.factura', $asesoria->factura_id);
                                        }
                                        $wa = "https://wa.me/{$phone}?text=" . urlencode($msg);
                                    @endphp
                                    <a href="{{ $wa }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 hover:text-green-900" title="Enviar recibo por WhatsApp">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 20l1.5-4.5A8.5 8.5 0 1112 20a8.4 8.4 0 01-3.6-.8L3 20z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.5 9.8c.2-.7.6-1.2 1.1-1.4.4-.2 1-.1 1.4.2.6.5 1.2 1.2 1.7 1.8.3.4.4 1 .2 1.4-.2.5-.7.9-1.4 1.1.7 1.1 1.6 2 2.7 2.7.2-.7.6-1.2 1.1-1.4.4-.2 1-.1 1.4.2.6.5 1.2 1.2 1.7 1.8.3.4.4 1 .2 1.4-.4.8-1.2 1.3-2.4 1.4-1.5.2-3.6-.7-5.6-2.7-2-2-2.9-4.1-2.7-5.6.1-1.2.6-2 1.4-2.4z" />
                                        </svg>
                                    </a>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <p class="mt-2 text-sm font-medium">No se encontraron asesorías</p>
                </div>
            @endforelse
        </div>
        
        <div class="p-4 border-t border-gray-200">
            {{ $asesorias->links() }}
        </div>
    </div>
    {{-- Modal Configuración de Campaña --}}
    @if($showCampaignModal)
        <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showCampaignModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    <div class="bg-indigo-600 px-8 py-6">
                        <h3 class="text-xl font-black text-white flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                            Control de Marketing
                        </h3>
                    </div>
                    <form wire:submit.prevent="saveCampaignConfig">
                        <div class="px-8 py-8 space-y-6">
                            <div class="space-y-2">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Nombre Técnico de la Campaña</label>
                                <input type="text" wire:model.defer="campaniaNombre" class="w-full bg-gray-50 border-0 ring-1 ring-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 transition" placeholder="Ej: CAMPAÑA MAYO 2026">
                                <p class="text-[10px] text-gray-400 px-1">Este es el tag que el sistema usará para crear los filtros automáticos.</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Mes Visible en la Web</label>
                                <input type="text" wire:model.defer="campaniaMes" class="w-full bg-gray-50 border-0 ring-1 ring-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 transition" placeholder="Ej: Mayo 2026">
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest px-1">Ruta de la Imagen del Póster</label>
                                <input type="text" wire:model.defer="campaniaPoster" class="w-full bg-gray-50 border-0 ring-1 ring-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 transition" placeholder="images/landings/bjca/tu_poster.jpg">
                                <p class="text-[10px] text-gray-400 px-1 italic">Ej: images/landings/bjca/campaign_april_2026.jpg</p>
                            </div>
                        </div>
                        <div class="px-8 pb-8 flex gap-3">
                            <button type="button" wire:click="$set('showCampaignModal', false)" class="flex-1 px-4 py-4 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Cancelar</button>
                            <button type="submit" class="flex-[2] px-4 py-4 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    window.addEventListener('open-url', (event) => {
        const data = event.detail?.[0] || event.detail || {};
        if (data.url) {
            window.open(data.url, '_blank');
        }
    });
</script>
@endpush
