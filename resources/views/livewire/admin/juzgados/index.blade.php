<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Juzgados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Actions -->
                    <div class="mb-4 flex justify-between items-center">
                        <x-text-input wire:model.live="search" type="text" class="w-full md:w-1/3" placeholder="Buscar juzgados..." />
                        
                        <x-primary-button wire:click="create">
                            {{ __('Nuevo Juzgado') }}
                        </x-primary-button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dirección
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Teléfono
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($juzgados as $juzgado)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $juzgado->nombre }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $juzgado->direccion }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $juzgado->telefono }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="edit({{ $juzgado->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                            <button wire:click="confirmDelete({{ $juzgado->id }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $juzgados->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <x-modal-wire wire:model="showModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editMode ? 'Editar Juzgado' : 'Crear Nuevo Juzgado' }}
            </h2>

            <div class="mt-6 space-y-6">
                <!-- Name -->
                <div>
                    <x-input-label for="nombre" :value="__('Nombre')" />
                    <x-text-input wire:model="nombre" id="nombre" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                </div>

                <!-- Direccion -->
                <div>
                    <x-input-label for="direccion" :value="__('Dirección')" />
                    <x-text-input wire:model="direccion" id="direccion" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                </div>

                <!-- Telefono -->
                <div>
                    <x-input-label for="telefono" :value="__('Teléfono')" />
                    <x-text-input wire:model="telefono" id="telefono" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
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

    <!-- Delete Confirmation Modal -->
    <x-modal-wire wire:model="confirmingDeletion">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('¿Estás seguro de que quieres eliminar este juzgado?') }}
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
