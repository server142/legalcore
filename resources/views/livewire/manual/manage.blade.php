<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gestionar Manual de Usuario') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="mb-6 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Páginas del Manual</h3>
                <x-primary-button wire:click="create">
                    {{ __('Nueva Página') }}
                </x-primary-button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Orden</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Imagen</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pages as $page)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $page->order }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $page->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($page->image_path)
                                        <img src="{{ asset('storage/' . $page->image_path) }}" class="h-10 w-20 object-cover rounded border">
                                    @else
                                        <span class="text-xs text-gray-400 italic">Sin imagen</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="edit({{ $page->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                    <button wire:click="delete({{ $page->id }})" wire:confirm="¿Seguro que deseas eliminar esta página?" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <x-modal-wire wire:model="showModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-6">
                {{ $editMode ? __('Editar Página') : __('Nueva Página') }}
            </h2>

            <div class="space-y-6">
                <div>
                    <x-input-label for="title" :value="__('Título')" />
                    <x-text-input wire:model="title" id="title" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="content" :value="__('Contenido')" />
                    <textarea wire:model="content" id="content" rows="10" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="order" :value="__('Orden')" />
                        <x-text-input wire:model="order" id="order" class="block mt-1 w-full" type="number" />
                        <x-input-error :messages="$errors->get('order')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="image" :value="__('Imagen de Pantalla')" />
                        <input type="file" wire:model="image" id="image" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>
                </div>

                @if ($image)
                    <div class="mt-4">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-2">Vista previa:</p>
                        <img src="{{ $image->temporaryUrl() }}" class="h-40 w-full object-cover rounded-xl border">
                    </div>
                @elseif ($existingImage)
                    <div class="mt-4">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-2">Imagen actual:</p>
                        <img src="{{ asset('storage/' . $existingImage) }}" class="h-40 w-full object-cover rounded-xl border">
                    </div>
                @endif
            </div>

            <div class="mt-8 flex justify-end">
                <x-secondary-button wire:click="$set('showModal', false)">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" wire:click="save">
                    {{ __('Guardar Página') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal-wire>
</div>
