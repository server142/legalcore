<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-500 text-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Mis Expedientes</h3>
            <p class="text-3xl font-bold">{{ $misExpedientesCount }}</p>
        </div>
        <div class="bg-orange-500 text-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Audiencias Próximas</h3>
            <p class="text-3xl font-bold">{{ $proximasAudienciasCount }}</p>
        </div>
        <div class="bg-red-500 text-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold">Términos Urgentes</h3>
            <p class="text-3xl font-bold">{{ $urgentTerminos->count() }}</p>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-4 border-b flex justify-between items-center bg-red-50">
            <h3 class="text-lg font-semibold text-red-800">Términos Urgentes</h3>
            <a href="{{ route('terminos.index') }}" class="text-sm text-red-600 hover:underline">Ver todos</a>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expediente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actuación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($urgentTerminos as $termino)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">
                            {{ \Carbon\Carbon::parse($termino->fecha_vencimiento)->format('d/m/Y') }}
                            <span class="text-xs font-normal text-gray-500">({{ \Carbon\Carbon::parse($termino->fecha_vencimiento)->diffForHumans() }})</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $termino->expediente->numero }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $termino->titulo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('expedientes.show', $termino->expediente_id) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No hay términos urgentes pendientes.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="block md:hidden divide-y divide-gray-200">
            @forelse($urgentTerminos as $termino)
            <a href="{{ route('expedientes.show', $termino->expediente_id) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start mb-1">
                    <span class="text-xs font-bold text-red-600 uppercase">
                        {{ \Carbon\Carbon::parse($termino->fecha_vencimiento)->format('d/m/Y') }}
                    </span>
                    <span class="text-[10px] text-gray-500">
                        {{ \Carbon\Carbon::parse($termino->fecha_vencimiento)->diffForHumans() }}
                    </span>
                </div>
                <h4 class="text-sm font-bold text-gray-900">{{ $termino->titulo }}</h4>
                <p class="text-xs text-gray-600 mt-1">Exp: {{ $termino->expediente->numero }}</p>
                <div class="mt-2 text-xs text-indigo-600 font-medium flex items-center">
                    Ver expediente
                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
            @empty
            <div class="p-4 text-center text-sm text-gray-500">
                No hay términos urgentes pendientes.
            </div>
            @endforelse
        </div>
    </div>

    @if(!auth()->user()->hasRole('super_admin'))
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Próximos 7 días</h3>
        <div class="space-y-4">
            @forelse($eventos as $evento)
                <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded-lg transition group">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-50 flex flex-col items-center justify-center text-indigo-600">
                        <span class="text-[10px] font-bold uppercase">{{ $evento->start_time->format('M') }}</span>
                        <span class="text-sm font-bold">{{ $evento->start_time->format('d') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800 truncate">{{ $evento->titulo }}</p>
                        <p class="text-xs text-gray-500">{{ $evento->start_time->format('H:i') }}</p>
                    </div>
                </div>
            @empty
                <p class="text-xs text-gray-400 italic">No hay eventos próximos.</p>
            @endforelse
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Mis Expedientes Asignados</h3>
            <a href="{{ route('expedientes.index') }}" class="text-sm text-blue-600 hover:underline">Ver todos</a>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Última Actuación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($misExpedientes as $exp)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                            <a href="{{ route('expedientes.show', $exp) }}">{{ $exp->numero }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $exp->cliente->nombre }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $exp->actuaciones()->latest()->first()?->titulo ?? 'Sin actuaciones' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('expedientes.show', $exp) }}" class="text-indigo-600 hover:text-indigo-900">Detalles</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="block md:hidden divide-y divide-gray-200">
            @foreach($misExpedientes as $exp)
            <a href="{{ route('expedientes.show', $exp) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start mb-1">
                    <span class="text-xs font-bold text-indigo-600 uppercase">{{ $exp->numero }}</span>
                </div>
                <h4 class="text-sm font-bold text-gray-900">{{ $exp->cliente->nombre }}</h4>
                <p class="text-xs text-gray-500 mt-1">
                    <span class="font-medium">Última:</span> {{ $exp->actuaciones()->latest()->first()?->titulo ?? 'Sin actuaciones' }}
                </p>
                <div class="mt-2 text-xs text-indigo-600 font-medium flex items-center">
                    Ver detalles
                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
            @endforeach
        </div>
    <!-- Monitor Inteligente (SJF / DOF) -->
    <div class="mt-8">
        <h3 class="text-xs font-bold text-gray-400 uppercase mb-4 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            Herramientas de Investigación
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('sjf.index') }}" class="bg-white p-4 rounded-xl border border-gray-100 hover:border-indigo-200 transition-all flex items-center justify-between group shadow-sm">
                <div>
                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Semanario</span>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($sjfCount) }} Tesis</p>
                </div>
                <div class="text-indigo-600 group-hover:translate-x-1 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </div>
            </a>
            <a href="{{ route('dof.index') }}" class="bg-white p-4 rounded-xl border border-gray-100 hover:border-amber-200 transition-all flex items-center justify-between group shadow-sm">
                <div>
                    <span class="text-[10px] font-black text-amber-400 uppercase tracking-widest">Diario Oficial</span>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($dofCount) }} Notas</p>
                </div>
                <div class="text-amber-600 group-hover:translate-x-1 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </div>
            </a>
        </div>
    </div>
</div>

