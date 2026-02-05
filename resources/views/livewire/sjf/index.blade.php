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
                        <div class="flex items-center">
                            <label for="useAI" class="inline-flex items-center cursor-pointer">
                                <span class="mr-3 text-sm font-medium text-indigo-900">Búsqueda Inteligente (IA)</span>
                                <div class="relative">
                                    <input type="checkbox" id="useAI" wire:model.live="useAI" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <div class="text-gray-500">
                        @if($useAI && $search)
                            <span class="flex items-center text-indigo-600 font-medium">
                                <svg class="w-4 h-4 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                                Usando IA para encontrar conceptos similares...
                            </span>
                        @else
                            Búsqueda tradicional por palabras exactas.
                        @endif
                    </div>
                    <div class="text-gray-400 italic">
                        Base de datos: {{ \App\Models\SjfPublication::count() }} tesis registradas.
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="overflow-x-auto">
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
                        @forelse($publications as $pub)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $pub->reg_digital }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 mb-1">
                                        {{ Str::limit($pub->rubro, 100) }}
                                    </div>
                                    <div class="text-xs text-cool-gray-500">
                                        {{ Str::limit($pub->loc, 50) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $pub->instancia }}</div>
                                    <div class="text-xs">{{ $pub->fecha_publicacion ? $pub->fecha_publicacion->format('d/m/Y') : '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="https://sjf2.scjn.gob.mx/sjfsist/paginas/DetalleGeneralV2.aspx?ID={{ $pub->reg_digital }}&Clase=DetalleTesisBL" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                        Ver Oficial
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron tesis registradas.
                                    <br>
                                    <span class="text-xs">Usa el botón de sincronizar o ejecuta el comando de importación.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $publications->links() }}
            </div>
        </div>
    </div>
</div>
