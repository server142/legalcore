<div>
    <x-slot name="header">
        <x-header title="{{ __('Documentos Legales') }}" subtitle="Gestión de Avisos de Privacidad, Términos y Condiciones, etc." />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Barra de Acciones -->
                <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="relative w-full md:w-1/3">
                        <input wire:model.live="search" type="text" placeholder="Buscar por nombre..." class="w-full rounded-lg border-gray-300 pl-10 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.legal-documents.create') }}" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nuevo Documento
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <!-- Desktop Table -->
                    <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                        <thead class="bg-gray-50 text-[10px] uppercase font-bold text-gray-500">
                            <tr>
                                <th class="px-6 py-3 text-left">Documento</th>
                                <th class="px-6 py-3 text-left">Tipo</th>
                                <th class="px-6 py-3 text-left">Versión</th>
                                <th class="px-6 py-3 text-center">Estado</th>
                                <th class="px-6 py-3 text-left">Visibilidad</th>
                                <th class="px-6 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($documents as $doc)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold text-gray-900">{{ $doc->nombre }}</div>
                                        <div class="text-[10px] text-gray-400">Publicado: {{ $doc->fecha_publicacion ? $doc->fecha_publicacion->format('d/m/Y') : 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $doc->tipo }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs text-gray-600 font-mono">{{ $doc->version }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button wire:click="toggleStatus({{ $doc->id }})" class="relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $doc->activo ? 'bg-emerald-500' : 'bg-gray-200' }}">
                                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $doc->activo ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @if($doc->visible_en)
                                                @foreach($doc->visible_en as $v)
                                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 text-[8px] text-gray-600 uppercase font-black">{{ $v }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-[9px] text-gray-400">Ninguno</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('admin.legal-documents.edit', $doc->id) }}" class="text-indigo-600 hover:text-indigo-900 transition" title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <button onclick="confirm('¿Estás seguro?') || event.stopImmediatePropagation()" wire:click="delete({{ $doc->id }})" class="text-red-600 hover:text-red-900 transition" title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic text-sm">
                                        No se encontraron documentos legales.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Mobile Cards -->
                    <div class="block md:hidden space-y-4">
                        @foreach($documents as $doc)
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 shadow-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $doc->nombre }}</div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $doc->tipo }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.legal-documents.edit', $doc->id) }}" class="p-1 text-indigo-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <button wire:click="toggleStatus({{ $doc->id }})" class="relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $doc->activo ? 'bg-emerald-500' : 'bg-gray-200' }}">
                                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $doc->activo ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex justify-between text-[10px] text-gray-500 mt-3 border-t pt-2">
                                    <span>Versión: {{ $doc->version }}</span>
                                    <span>Pub: {{ $doc->fecha_publicacion ? $doc->fecha_publicacion->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6">
                    {{ $documents->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
