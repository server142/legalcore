<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
    <div class="mb-8 text-center space-y-4">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
            Buscador del Diario Oficial
        </h2>
        <p class="text-lg text-gray-600">
            Encuentra decretos, acuerdos y notificaciones con el poder de la búsqueda inteligente.
        </p>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">¿Qué estás buscando?</label>
        <div class="relative rounded-md shadow-sm">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg py-3" 
                placeholder="Ej. Reformas fiscales 2024, Vacaciones dignas..."
            >
             <div class="absolute inset-y-0 right-0 flex py-1.5 pr-1.5">
                <kbd class="inline-flex items-center rounded border border-gray-200 px-2 font-sans text-xs font-medium text-gray-400">⌘K</kbd>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="mt-4 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="date_from" class="block text-xs font-medium text-gray-500 mb-1">Desde</label>
                <input type="date" wire:model.live="dateFrom" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="flex-1">
                <label for="date_to" class="block text-xs font-medium text-gray-500 mb-1">Hasta</label>
                <input type="date" wire:model.live="dateTo" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="flex items-end">
                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-2 text-xs font-medium text-green-800">
                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                    Buscador Semántico Listo
                </span>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="space-y-4">
        @forelse($results as $result)
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <a href="{{ $result->link_pdf }}" target="_blank" class="text-xl font-semibold text-indigo-600 hover:underline block mb-1">
                        {{ $result->titulo }}
                    </a>
                    <span class="inline-flex items-center rounded bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                        {{ $result->fecha_publicacion->format('d M, Y') }}
                    </span>
                </div>
                
                @if($result->organismo)
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-bold mb-2">{{ $result->organismo }}</p>
                @endif
                
                <p class="text-gray-700 text-sm mb-4 line-clamp-3">
                    {{ $result->resumen }}
                </p>
                
                <div class="flex items-center gap-2">
                    <a href="{{ $result->link_pdf }}" target="_blank" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Ver PDF Original
                    </a>
                    @if($result->seccion)
                    <span class="text-gray-300">&bull;</span>
                    <span class="text-sm text-gray-500">{{ $result->seccion }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No se encontraron resultados</h3>
                <p class="mt-1 text-sm text-gray-500">Intenta ajustar tu búsqueda o filtros.</p>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $results->links() }}
        </div>
    </div>
</div>
