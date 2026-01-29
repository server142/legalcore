<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-indigo-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Expedientes Activos</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $activeExpedientes }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Vencimientos Próximos</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $upcomingDeadlines }}</p>
        </div>
        @can('manage billing')
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Total Cobrado</h3>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($totalCobrado, 2) }}</p>
            <a href="{{ route('reportes.ingresos') }}" class="mt-2 inline-block text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">Ver detalle por periodo</a>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-orange-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Pendiente de Cobro</h3>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($pendienteCobro, 2) }}</p>
        </div>
        @else
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Total Clientes</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $totalClientes }}</p>
        </div>
        @endcan
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b">
                <h3 class="text-lg font-semibold">Últimos Expedientes</h3>
            </div>
            
            <!-- Desktop Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentExpedientes as $exp)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2 text-sm font-medium text-indigo-600">
                                <a href="{{ route('expedientes.show', $exp) }}">{{ $exp->numero }}</a>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $exp->titulo }}</td>
                            <td class="px-4 py-2 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $exp->estadoProcesal?->nombre ?? $exp->estado_procesal }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm font-medium">
                                <a href="{{ route('expedientes.show', $exp) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-gray-200">
                @foreach($recentExpedientes as $exp)
                <a href="{{ route('expedientes.show', $exp) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-indigo-600 uppercase">{{ $exp->numero }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $exp->estadoProcesal?->nombre ?? $exp->estado_procesal }}
                        </span>
                    </div>
                    <h4 class="text-sm font-bold text-gray-900">{{ $exp->titulo }}</h4>
                    <div class="mt-2 flex items-center text-xs text-indigo-600 font-medium">
                        Ver detalles
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b flex justify-between items-center bg-red-50">
                <h3 class="text-lg font-semibold text-red-800">Términos Urgentes</h3>
                <a href="{{ route('terminos.index') }}" class="text-xs text-red-600 hover:underline font-bold">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($urgentTerminos as $termino)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-900">{{ $termino->titulo }}</p>
                                <p class="text-xs text-gray-500">Exp: {{ $termino->expediente->numero }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold {{ $termino->fecha_vencimiento->isPast() ? 'text-red-600' : 'text-orange-600' }}">
                                    {{ $termino->fecha_vencimiento->format('d/m/Y') }}
                                </p>
                                <p class="text-[10px] text-gray-400 uppercase">{{ $termino->fecha_vencimiento->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 italic">
                        No hay términos pendientes urgentes.
                    </div>
                @endforelse
            </div>
        </div>

        @if(!auth()->user()->hasRole('super_admin'))
        <div class="bg-white rounded-lg shadow overflow-hidden mt-6 md:mt-0">
            <div class="p-4 border-b flex justify-between items-center bg-indigo-50">
                <h3 class="text-lg font-semibold text-indigo-800">Próximos 7 días</h3>
                <a href="{{ route('agenda.index') }}" class="text-xs text-indigo-600 hover:underline font-bold">Ver Agenda</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($eventos as $evento)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-100 flex flex-col items-center justify-center text-indigo-600">
                                <span class="text-[10px] font-bold uppercase">{{ $evento->start_time->format('M') }}</span>
                                <span class="text-sm font-bold">{{ $evento->start_time->format('d') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800 truncate">{{ $evento->titulo }}</p>
                                <p class="text-xs text-gray-500">{{ $evento->start_time->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 italic">
                        No hay eventos próximos.
                    </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
</div>
