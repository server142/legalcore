<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Semanario Judicial de la Federación') }}
            </h2>
            
            <div class="flex space-x-2">
                 <!-- Sync Button currently placeholder for future implementation -->
                 <button wire:click="syncLatest" wire:loading.attr="disabled" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="syncLatest">Sincronizar Recientes</span>
                    <span wire:loading wire:target="syncLatest">Sincronizando...</span>
                </button>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <!-- Search Bar -->
            <div class="mb-6 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="relative flex-1">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por rubro, concepto o registro..." class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100">
                        <div class="flex items-center text-indigo-700 font-semibold text-sm">
                            <svg class="w-5 h-5 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Motor de IA Activo
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <div class="text-gray-500 flex flex-wrap gap-2">
                        @if($search)
                            <span class="flex items-center text-indigo-600 font-medium">
                                <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                Analizando para "{{ $search }}"...
                            </span>
                            <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-bold">
                                coincidencias: {{ $publications->total() }}
                            </span>
                        @else
                            Busca conceptos, frases o números de registro digital.
                        @endif
                    </div>
                    <div class="text-gray-400 italic">
                        Base de datos: {{ \App\Models\SjfPublication::count() }} docs. registrados.
                    </div>
                </div>
            </div>

            <!-- Mobile: Cards Layout -->
            <div class="md:hidden space-y-4">
                @forelse($publications as $pub)
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <!-- Top: Registration Number -->
                    <div class="mb-1">
                        <span class="text-[9px] font-black tracking-widest text-indigo-400 uppercase">
                            REG. {{ $pub->reg_digital }}
                        </span>
                    </div>

                    <!-- Title (Rubro) -->
                    <div class="mb-3">
                        <h4 class="text-sm font-extrabold text-gray-900 leading-tight">
                            {{ $pub->rubro }}
                        </h4>
                    </div>

                    <!-- Metadata line style from image -->
                    <div class="text-[11px] text-gray-500 leading-relaxed mb-4 font-medium italic">
                        @php
                            $metadata = array_filter([
                                $pub->instancia,
                                $pub->epoca,
                                $pub->fuente,
                                $pub->localizacion
                            ]);
                        @endphp
                        {{ implode('; ', $metadata) }}
                    </div>

                    <!-- Footer: Date and Action Button -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                        <span class="text-[10px] font-black tracking-widest text-indigo-300 uppercase">
                            {{ $pub->fecha_publicacion ? $pub->fecha_publicacion->format('d/m/Y') : '' }}
                        </span>
                        <a href="https://sjf2.scjn.gob.mx/detalle/tesis/{{ $pub->reg_digital }}" target="_blank" class="text-xs font-bold text-indigo-700 bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100 transition-all active:scale-95">
                            Ver detalle
                        </a>
                    </div>
                </div>
                @empty
                    <div class="text-center py-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                        <p class="text-sm text-gray-400">No se encontraron documentos.</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop: Results Table -->
            <div class="hidden md:block overflow-x-auto border border-gray-100 rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reg. Digital
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rubro
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Instancia / Fecha
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($publications as $pub)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                    {{ $pub->reg_digital }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 mb-1 leading-snug">
                                        {{ Str::limit($pub->rubro, 150) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ Str::limit($pub->localizacion, 80) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="font-medium text-gray-700">{{ $pub->instancia }}</div>
                                    <div class="text-xs">{{ $pub->fecha_publicacion ? $pub->fecha_publicacion->format('d/m/Y') : '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="https://sjf2.scjn.gob.mx/detalle/tesis/{{ $pub->reg_digital }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 rounded-md hover:bg-indigo-100 transition-colors">
                                        <span>Ver Oficial</span>
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $publications->links() }}
            </div>
        </div>
    </div>
</div>
