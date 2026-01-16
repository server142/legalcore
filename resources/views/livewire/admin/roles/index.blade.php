<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Roles y Permisos</h2>
        <x-primary-button wire:click="create">
            {{ __('Nuevo Rol') }}
        </x-primary-button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre del Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permisos</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($roles as $role)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ ucfirst($role->name) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach($role->permissions as $permission)
                                <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-indigo-100 text-indigo-800">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="edit({{ $role->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                        @if(!in_array($role->name, ['admin', 'super_admin']))
                            <button wire:click="delete({{ $role->id }})" wire:confirm="¿Estás seguro de eliminar este rol?" class="text-red-600 hover:text-red-900">Eliminar</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $roles->links() }}
    </div>

    <!-- Modal de Rol -->
    <x-modal-wire wire:model="showModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ $roleId ? 'Editar Rol' : 'Crear Nuevo Rol' }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" :value="__('Nombre del Rol')" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" placeholder="ej. Abogado Senior" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label :value="__('Permisos Asignados')" />
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-60 overflow-y-auto p-2 border rounded-md">
                        @foreach($permissions as $permission)
                        <label class="flex items-center space-x-2 p-1 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->name }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="text-sm text-gray-600">{{ $permission->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button wire:click="$set('showModal', false)">
                    {{ __('Cancelar') }}
                </x-secondary-button>
                <x-primary-button wire:click="save">
                    {{ __('Guardar Rol') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal-wire>
</div>
