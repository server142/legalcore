<div>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.plans.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition text-gray-500 flex-shrink-0 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $plan_id ? __('Editar Plan') : __('Nuevo Plan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form wire:submit="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div>
                                <x-input-label for="name" :value="__('Nombre del Plan')" />
                                <x-text-input wire:model.live="name" id="name" class="block mt-1 w-full" type="text" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Slug -->
                            <div>
                                <x-input-label for="slug" :value="__('Slug (URL amigable)')" />
                                <x-text-input wire:model="slug" id="slug" class="block mt-1 w-full" type="text" required />
                                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                            </div>

                            <!-- Stripe Price ID -->
                            <div>
                                <x-input-label for="stripe_price_id" :value="__('Stripe Price ID (price_...)')" />
                                <x-text-input wire:model="stripe_price_id" id="stripe_price_id" class="block mt-1 w-full" type="text" placeholder="price_1Q..." />
                                <p class="text-xs text-gray-500 mt-1">Obtenlo del catálogo de productos en Stripe.</p>
                                <x-input-error :messages="$errors->get('stripe_price_id')" class="mt-2" />
                            </div>

                            <!-- Precio -->
                            <div>
                                <x-input-label for="price" :value="__('Precio (MXN)')" />
                                <x-text-input wire:model="price" id="price" class="block mt-1 w-full" type="number" step="0.01" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <!-- Duración -->
                            <div>
                                <x-input-label for="duration_in_days" :value="__('Duración (días)')" />
                                <x-text-input wire:model="duration_in_days" id="duration_in_days" class="block mt-1 w-full" type="number" required />
                                <x-input-error :messages="$errors->get('duration_in_days')" class="mt-2" />
                            </div>

                            <!-- Límites de Usuarios -->
                            <div>
                                <x-input-label for="max_admin_users" :value="__('Máximo Usuarios Admin')" />
                                <x-text-input wire:model="max_admin_users" id="max_admin_users" class="block mt-1 w-full" type="number" min="1" required />
                                <p class="text-xs text-gray-500 mt-1">Generalmente 1 para la mayoría de planes.</p>
                                <x-input-error :messages="$errors->get('max_admin_users')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="max_lawyer_users" :value="__('Máximo Usuarios Abogados')" />
                                <x-text-input wire:model="max_lawyer_users" id="max_lawyer_users" class="block mt-1 w-full" type="number" min="0" placeholder="Dejar vacío para ilimitado" />
                                <p class="text-xs text-gray-500 mt-1">Dejar en blanco para usuarios ilimitados.</p>
                                <x-input-error :messages="$errors->get('max_lawyer_users')" class="mt-2" />
                            </div>

                            <!-- Límite de Expedientes -->
                            <div>
                                <x-input-label for="max_expedientes" :value="__('Máximo de Expedientes')" />
                                <x-text-input wire:model="max_expedientes" id="max_expedientes" class="block mt-1 w-full" type="number" min="0" placeholder="0 = ilimitado" />
                                <p class="text-xs text-gray-500 mt-1">0 para expedientes ilimitados. Ej: 10, 50, 100.</p>
                                <x-input-error :messages="$errors->get('max_expedientes')" class="mt-2" />
                            </div>

                            <!-- Límite de Almacenamiento -->
                            <div>
                                <x-input-label for="storage_limit_gb" :value="__('Límite de Almacenamiento (GB)')" />
                                <x-text-input wire:model="storage_limit_gb" id="storage_limit_gb" class="block mt-1 w-full" type="number" min="1" required />
                                <p class="text-xs text-gray-500 mt-1">Espacio total disponible para documentos (en Gigabytes).</p>
                                <x-input-error :messages="$errors->get('storage_limit_gb')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Características -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Características del Plan</h3>
                            
                            <div class="flex gap-2 mb-4">
                                <x-text-input wire:model="newFeature" wire:keydown.enter.prevent="addFeature" placeholder="Agregar nueva característica..." class="flex-1" />
                                <button type="button" wire:click="addFeature" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">Agregar</button>
                            </div>

                            <ul class="space-y-2">
                                @foreach($features as $index => $feature)
                                    <li class="flex items-center justify-between bg-gray-50 p-3 rounded-md">
                                        <span>{{ $feature }}</span>
                                        <button type="button" wire:click="removeFeature({{ $index }})" class="text-red-600 hover:text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Estado -->
                        <div class="mt-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-gray-700">Plan Activo (visible para suscripción)</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('admin.plans.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>
                                {{ __('Guardar Plan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
