<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Anuncios y Bienvenida') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4 flex justify-between items-center">
                <div class="flex-1 pr-4">
                    <x-text-input wire:model.live="search" placeholder="Buscar anuncios..." class="w-full" />
                </div>
                <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo Anuncio
                </button>
            </div>

            @if (session()->has('message'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Público</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leído por</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($announcements as $announcement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $announcement->title }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($announcement->message, 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $announcement->target_role ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $announcement->target_role ? ucfirst(str_replace('_', ' ', $announcement->target_role)) : 'Todos' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $announcement->readers()->count() }} usuarios
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button wire:click="toggleStatus({{ $announcement->id }})" 
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $announcement->is_active ? 'bg-indigo-600' : 'bg-gray-200' }}">
                                        <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $announcement->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="edit({{ $announcement->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                    <button wire:click="delete({{ $announcement->id }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    No hay anuncios creados aún.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Management Modal -->
    <x-modal name="announcement-modal" :show="$confirmingAnnouncementManagement" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $announcementId ? 'Editar Anuncio' : 'Crear Nuevo Anuncio' }}
            </h2>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="title" value="Título" />
                    <x-text-input wire:model="title" id="title" class="block w-full mt-1" type="text" placeholder="Ej: Mantenimiento Programado" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="message" value="Mensaje / Contenido" />
                    <textarea wire:model="message" id="message" rows="4" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Detalles del anuncio..."></textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="video_url" value="URL del Video (Opcional)" />
                    <x-text-input wire:model="video_url" id="video_url" class="block w-full mt-1" type="text" placeholder="https://youtube.com/..." />
                    <p class="text-xs text-gray-500 mt-1">YouTube o enlace directo MP4.</p>
                    <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="target_role" value="Dirigido a" />
                    <select wire:model="target_role" id="target_role" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all">Todos los Usuarios</option>
                        <option value="super_admin">Sólo Super Admins</option>
                        <option value="tenant_admin">Admins de Despacho</option>
                        <option value="regular_user">Usuarios Normales</option>
                    </select>
                    <x-input-error :messages="$errors->get('target_role')" class="mt-2" />
                </div>

                <div class="flex items-center">
                    <input type="checkbox" wire:model="is_active" id="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-600">Publicar Inmediatamente</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('confirmingAnnouncementManagement', false)">
                    Cancelar
                </x-secondary-button>

                <x-primary-button class="ml-3" wire:click="save">
                    Guardar Anuncio
                </x-primary-button>
            </div>
        </div>
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-modal name="confirm-delete-modal" :show="$confirmingAnnouncementDeletion" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                ¿Estás seguro de eliminar este anuncio?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Esta acción no se puede deshacer. Se eliminarán los registros de lectura de los usuarios.
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('confirmingAnnouncementDeletion', false)">
                    Cancelar
                </x-secondary-button>

                <x-danger-button class="ml-3" wire:click="confirmDelete">
                    Eliminar Anuncio
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>
