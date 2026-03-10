<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Abogados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Actions -->
                    <div class="mb-4 flex justify-between items-center">
                        <x-text-input wire:model.live="search" type="text" class="w-full md:w-1/3" placeholder="Buscar abogados..." />
                        
                        <x-primary-button wire:click="create">
                            {{ __('Nuevo Abogado') }}
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
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($abogados as $abogado)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $abogado->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $abogado->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="resendInvitation({{ $abogado->id }})" 
                                                    wire:confirm="¿Estás seguro de que deseas reenviar la invitación? Se generará una nueva contraseña temporal."
                                                    class="text-green-600 hover:text-green-900 mr-3" title="Reenviar Invitación">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                            </button>
                                            <button wire:click="edit({{ $abogado->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                            <button wire:click="confirmDelete({{ $abogado->id }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $abogados->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <x-modal-wire wire:model="showModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editMode ? 'Editar Abogado' : 'Crear Nuevo Abogado' }}
            </h2>

            <div class="mt-6 space-y-6">
                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nombre')" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Contraseña')" />
                    <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" :required="!$editMode" />
                    <p class="text-xs text-gray-500 mt-1">{{ $editMode ? 'Dejar en blanco para mantener la actual' : '' }}</p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                @if(!$editMode || $password)
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" :required="!$editMode" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
                @endif
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
                {{ __('¿Estás seguro de que quieres eliminar este abogado?') }}
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
