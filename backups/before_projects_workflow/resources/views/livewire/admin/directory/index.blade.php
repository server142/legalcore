<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-medium text-gray-900">
                        Administración del Directorio Público
                    </h1>
                    
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar abogado..." class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>
                </div>

                <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Abogado</th>
                                <th scope="col" class="px-6 py-3">Ubicación</th>
                                <th scope="col" class="px-6 py-3">Titular</th>
                                <th scope="col" class="px-6 py-3 text-center">Estado</th>
                                <th scope="col" class="px-6 py-3 text-center">Verificado</th>
                                <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($profiles as $profile)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($profile->user->profile_photo_path)
                                                <img class="w-8 h-8 rounded-full mr-2" src="{{ $profile->user->profile_photo_url }}" alt="">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-500 flex items-center justify-center mr-2 font-bold">
                                                    {{ substr($profile->user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold">{{ $profile->user->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $profile->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $profile->city }}, {{ $profile->state }}
                                    </td>
                                    <td class="px-6 py-4 truncate max-w-xs" title="{{ $profile->headline }}">
                                        {{ $profile->headline }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button wire:click="toggleVisibility({{ $profile->id }})" class="relative inline-flex items-center cursor-pointer">
                                            <span class="{{ $profile->is_public ? 'bg-green-500' : 'bg-gray-200' }} inline-block h-6 w-11 border-2 border-transparent rounded-full transition-colors ease-in-out duration-200"></span>
                                            <span class="{{ $profile->is_public ? 'translate-x-5' : 'translate-x-0' }} inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 pointer-events-none absolute top-0.5 left-0.5"></span>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button wire:click="toggleVerification({{ $profile->id }})" class="text-xl" title="Clic para cambiar estado">
                                            {{ $profile->is_verified ? '🏅' : '⚪' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="confirmDeletion({{ $profile->id }})" class="font-medium text-red-600 hover:underline">Eliminar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron perfiles.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $profiles->links() }}
                </div>

            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal wire:model.live="confirmingDeletion">
        <x-slot name="title">
            Eliminar Perfil del Directorio
        </x-slot>

        <x-slot name="content">
            ¿Estás seguro de que quieres eliminar este perfil del directorio público? Esta acción ocultará al abogado del directorio, pero no eliminará su cuenta de usuario.
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmingDeletion')" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            <x-danger-button class="ml-2" wire:click="deleteProfile" wire:loading.attr="disabled">
                Eliminar Perfil
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
