<div class="p-6">
    <x-slot name="header">
        <x-header title="Gestión de Proveedores de IA" backUrl="{{ route('admin.global-settings') }}" />
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <!-- Header Actions -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Proveedores de IA Configurados</h2>
                <p class="text-sm text-gray-600 mt-1">Gestiona las credenciales de diferentes servicios de Inteligencia Artificial</p>
            </div>
            <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Agregar Proveedor
            </button>
        </div>

        <!-- Providers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($providers as $provider)
                @php
                    $activeProvider = \App\Models\AiProvider::getActive();
                    $isActive = $activeProvider && $activeProvider->id === $provider->id;
                @endphp
                <div class="bg-white rounded-lg shadow-md p-6 border-2 {{ $isActive ? 'border-green-500' : 'border-gray-200' }}">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                {{ $provider->name }}
                                @if($isActive)
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">Activo</span>
                                @endif
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $provider->slug }}</p>
                        </div>
                        <div class="flex gap-1">
                            <button wire:click="edit({{ $provider->id }})" class="text-blue-600 hover:text-blue-800 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button wire:click="delete({{ $provider->id }})" wire:confirm="¿Eliminar este proveedor?" class="text-red-600 hover:text-red-800 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="space-y-2 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">Modelo por Defecto:</p>
                            <p class="text-sm font-mono text-gray-800">{{ $provider->default_model }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">API Key:</p>
                            <p class="text-sm font-mono text-gray-800 break-all">{{ $provider->masked_api_key }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Estado:</p>
                            <span class="text-sm {{ $provider->is_active ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $provider->is_active ? '✓ Habilitado' : '✗ Deshabilitado' }}
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        @if(!$isActive)
                            <button wire:click="setActive({{ $provider->id }})" class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 px-3 py-2 rounded-lg text-sm font-medium transition">
                                Activar
                            </button>
                        @endif
                        <button wire:click="testConnection({{ $provider->id }})" wire:loading.attr="disabled" class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-2 rounded-lg text-sm font-medium transition">
                            <span wire:loading.remove wire:target="testConnection({{ $provider->id }})">Probar</span>
                            <span wire:loading wire:target="testConnection({{ $provider->id }})">Probando...</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay proveedores configurados</h3>
                    <p class="text-gray-600 mb-4">Agrega tu primer proveedor de IA para comenzar</p>
                    <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">
                        Agregar Proveedor
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Modal -->
        @if($showModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-800">
                                {{ $editingId ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
                            </h3>
                            <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="save" class="space-y-4">
                            <div>
                                <x-input-label for="name" value="Nombre del Proveedor" />
                                <x-text-input wire:model.live="name" id="name" class="mt-1 block w-full" type="text" placeholder="ej. OpenAI, Anthropic, Groq" />
                                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <x-input-label for="slug" value="Identificador (slug)" />
                                <x-text-input wire:model="slug" id="slug" class="mt-1 block w-full" type="text" placeholder="ej. openai, anthropic" />
                                <p class="text-xs text-gray-500 mt-1">Se genera automáticamente del nombre. Solo letras minúsculas y guiones.</p>
                                @error('slug') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <x-input-label for="default_model" value="Modelo por Defecto" />
                                <x-text-input wire:model="default_model" id="default_model" class="mt-1 block w-full" type="text" placeholder="ej. gpt-4o-mini, claude-3-5-sonnet-20241022" />
                                @error('default_model') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <x-input-label for="api_key" value="API Key" />
                                <x-text-input wire:model="api_key" id="api_key" class="mt-1 block w-full font-mono text-sm" type="password" placeholder="sk-..." />
                                <p class="text-xs text-gray-500 mt-1">Se guardará encriptada en la base de datos.</p>
                                @error('api_key') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center">
                                <input wire:model="is_active" id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="is_active" class="ml-2 text-sm text-gray-700">Proveedor habilitado</label>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                    Cancelar
                                </button>
                                <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Actualizar' : 'Guardar' }}</span>
                                    <span wire:loading wire:target="save">Guardando...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
