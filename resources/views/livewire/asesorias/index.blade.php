<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Asesorías') }}
    </h2>
</x-slot>

<div class="p-4 md:p-6 space-y-6">
    {{-- Header y Botón Nuevo --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Control de Asesorías</h2>
            <p class="text-sm text-gray-500">Gestiona citas, seguimientos y conversiones a clientes</p>
        </div>
        <a href="{{ route('asesorias.create') }}" class="w-full md:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-center font-bold shadow-sm flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nueva Asesoría
        </a>
    </div>

    {{-- Tarjetas de Estadísticas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
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
                                    <span class="text-sm font-medium text-gray-900">{{ $asesoria->nombre_prospecto }}</span>
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
                                <a href="{{ route('asesorias.edit', $asesoria) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 font-bold">Gestionar</a>
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
        
        <div class="p-4 border-t border-gray-200">
            {{ $asesorias->links() }}
        </div>
    </div>
</div>
