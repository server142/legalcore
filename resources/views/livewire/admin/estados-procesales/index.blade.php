<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Estados Procesales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4 flex justify-between items-center">
                        <x-text-input wire:model.live="search" type="text" class="w-full md:w-1/3" placeholder="Buscar estados..." />

                        @if($canWrite)
                        <x-primary-button wire:click="create">
                            {{ __('Nuevo Estado') }}
                        </x-primary-button>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descripción
                                    </th>
                                    @if($canWrite)
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($estados as $estado)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $estado->nombre }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">{{ $estado->descripcion }}</div>
                                        </td>
                                        @if($canWrite)
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="edit({{ $estado->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                            <button wire:click="confirmDelete({{ $estado->id }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $estados->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <x-modal-wire wire:model="showModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editMode ? 'Editar Estado Procesal' : 'Crear Nuevo Estado Procesal' }}
            </h2>

            <div class="mt-6 space-y-6">
                <div>
                    <x-input-label for="nombre" :value="__('Nombre')" />
                    <x-text-input wire:model="nombre" id="nombre" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="descripcion" :value="__('Descripción')" />
                    <textarea wire:model="descripcion" id="descripcion" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('showModal', false)">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" wire:click="{{ $editMode ? 'update' : 'store' }}">
                    {{ $editMode ? __('Actualizar') : __('Guardar') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal-wire>

    <x-modal-wire wire:model="confirmingDeletion">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('¿Estás seguro de que quieres eliminar este estado procesal?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Esta acción no se puede deshacer.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('confirmingDeletion', false)">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ml-3" wire:click="delete">
                    {{ __('Eliminar') }}
                </x-danger-button>
            </div>
        </div>
    </x-modal-wire>
</div>
