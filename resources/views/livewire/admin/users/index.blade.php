<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Actions -->
                    <div class="mb-4 flex justify-between items-center">
                        <x-text-input wire:model.live="search" type="text" class="w-full md:w-1/3" placeholder="Buscar usuarios..." />
                        
                        <x-primary-button wire:click="create">
                            {{ __('Nuevo Usuario') }}
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Roles
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @foreach($user->roles as $role)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="edit({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                            <button wire:click="confirmDelete({{ $user->id }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->

    <x-modal-wire wire:model="showModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $editMode ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}
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

                <!-- Roles -->
                <div>
                    <x-input-label for="roles" :value="__('Roles')" />
                    <div class="mt-2 space-y-2">
                        @foreach($roles as $role)
                            <div class="flex items-center">
                                <input wire:model="selectedRoles" id="role_{{ $role->id }}" type="checkbox" value="{{ $role->name }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-600">{{ $role->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('selectedRoles')" class="mt-2" />
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
    <x-modal-wire wire:model="confirmingUserDeletion">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('¿Estás seguro de que quieres eliminar este usuario?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Una vez que se elimine la cuenta, todos sus recursos y datos se eliminarán permanentemente.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('confirmingUserDeletion', false)">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ml-3" wire:click="deleteUser">
                    {{ __('Eliminar Usuario') }}
                </x-danger-button>
            </div>
        </div>
    </x-modal-wire>
</div>
