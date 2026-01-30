<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Tenants (Despachos)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session()->has('message'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-text-input wire:model.live="search" placeholder="Buscar por nombre, dominio..." class="w-full" />
                    </div>
                    <div>
                        <select wire:model.live="filterStatus" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all">Todos los estados</option>
                            <option value="trial">En Trial</option>
                            <option value="active">Suscripciones Activas</option>
                            <option value="expired">Trials Expirados</option>
                            <option value="cancelled">Cancelados/Inactivos</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-sm text-gray-500 mb-1">Total Tenants</div>
                    <div class="text-3xl font-bold text-gray-900">{{ \App\Models\Tenant::count() }}</div>
                </div>
                <div class="bg-blue-50 p-6 rounded-xl shadow-sm border border-blue-100">
                    <div class="text-sm text-blue-600 mb-1">En Trial</div>
                    <div class="text-3xl font-bold text-blue-900">{{ \App\Models\Tenant::where('plan', 'trial')->count() }}</div>
                </div>
                <div class="bg-green-50 p-6 rounded-xl shadow-sm border border-green-100">
                    <div class="text-sm text-green-600 mb-1">Activos Pagados</div>
                    <div class="text-3xl font-bold text-green-900">{{ \App\Models\Tenant::where('plan', '!=', 'trial')->where('is_active', true)->count() }}</div>
                </div>
                <div class="bg-red-50 p-6 rounded-xl shadow-sm border border-red-100">
                    <div class="text-sm text-red-600 mb-1">Trials Expirados</div>
                    <div class="text-3xl font-bold text-red-900">{{ \App\Models\Tenant::where('plan', 'trial')->where('trial_ends_at', '<', now())->count() }}</div>
                </div>
            </div>

            <!-- Tenants Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <!-- Desktop Table -->
                    <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Actual</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuarios</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tenants as $tenant)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $tenant->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $tenant->plan === 'trial' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ $tenant->planRelation ? $tenant->planRelation->name : ucfirst($tenant->plan) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($tenant->expiration_date)
                                            <div class="{{ $tenant->is_expired ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                                {{ $tenant->expiration_date->format('d/m/Y') }}
                                            </div>
                                            @if(!$tenant->is_expired)
                                                <div class="text-xs text-green-600">{{ now()->diffInDays($tenant->expiration_date) }} días restantes</div>
                                            @else
                                                <div class="text-xs text-red-500">Expirado</div>
                                            @endif
                                        @else
                                            <span class="text-gray-400">Sin fecha</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $tenant->users->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button wire:click="toggleStatus({{ $tenant->id }})" class="px-2 py-1 text-xs font-semibold rounded-full {{ $tenant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $tenant->is_active ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-3">
                                            @if($tenant->plan === 'trial')
                                                <button wire:click="extendTrial({{ $tenant->id }}, 15)" class="text-blue-600 hover:text-blue-900 text-xs">
                                                    +15 días
                                                </button>
                                            @endif
                                            
                                            <button type="button" wire:click="openEditModal({{ $tenant->id }})" class="text-indigo-600 hover:text-indigo-900">
                                                Editar
                                            </button>
                                            
                                            <button type="button" wire:click="openPlanChangeModal({{ $tenant->id }})" class="text-blue-600 hover:text-blue-900">
                                                Plan
                                            </button>

                                            <button type="button" wire:click="resetWelcomeForTenant({{ $tenant->id }})" class="text-amber-600 hover:text-amber-900" title="Volver a mostrar video de bienvenida a todos los usuarios">
                                                Reset Welcome
                                            </button>

                                            <button type="button" wire:click="deleteTenant({{ $tenant->id }})" wire:confirm="¿ESTÁS SEGURO? Esta acción eliminará permanentemente al tenant '{{ $tenant->name }}' y ABSOLUTAMENTE TODOS sus datos (usuarios, expedientes, documentos, etc). Esta acción no se puede deshacer." class="text-red-600 hover:text-red-900">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        No se encontraron tenants que coincidan con los filtros.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Mobile Cards -->
                    <div class="block md:hidden">
                        @forelse($tenants as $tenant)
                        <div class="p-4 border-b border-gray-200 hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $tenant->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $tenant->slug }}</p>
                                </div>
                                <button wire:click="toggleStatus({{ $tenant->id }})" class="px-2 py-1 text-xs font-semibold rounded-full {{ $tenant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $tenant->is_active ? 'Activo' : 'Inactivo' }}
                                </button>
                            </div>
                            
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $tenant->plan === 'trial' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    Plan: {{ $tenant->planRelation ? $tenant->planRelation->name : ucfirst($tenant->plan) }}
                                </span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $tenant->users->count() }} Usuarios
                                </span>
                            </div>

                            <div class="mb-4 text-sm">
                                @if($tenant->expiration_date)
                                    <div class="flex items-center">
                                        <span class="text-gray-500 mr-2">Vencimiento:</span>
                                        <span class="{{ $tenant->is_expired ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ $tenant->expiration_date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    @if(!$tenant->is_expired)
                                        <div class="text-xs text-green-600 mt-1">{{ now()->diffInDays($tenant->expiration_date) }} días restantes</div>
                                    @else
                                        <div class="text-xs text-red-500 mt-1">Expirado</div>
                                    @endif
                                @endif
                            </div>

                            <div class="flex justify-end space-x-3 border-t pt-3">
                                @if($tenant->plan === 'trial')
                                    <button wire:click="extendTrial({{ $tenant->id }}, 15)" class="text-blue-600 font-medium text-sm hover:text-blue-800">
                                        +15 días
                                    </button>
                                @endif
                                <button type="button" wire:click="openEditModal({{ $tenant->id }})" class="text-indigo-600 font-medium text-sm hover:text-indigo-800">
                                    Editar
                                </button>
                                <button type="button" wire:click="openPlanChangeModal({{ $tenant->id }})" class="text-blue-600 font-medium text-sm hover:text-blue-800">
                                    Plan
                                </button>
                                <button type="button" wire:click="resetWelcomeForTenant({{ $tenant->id }})" class="text-amber-600 font-medium text-sm hover:text-amber-800">
                                    Reset Welcome
                                </button>
                                <button type="button" wire:click="deleteTenant({{ $tenant->id }})" wire:confirm="¿Eliminar permanentemente '{{ $tenant->name }}' y todos sus datos?" class="text-red-600 font-medium text-sm hover:text-red-800">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                        @empty
                            <div class="p-6 text-center text-gray-500">
                                No se encontraron tenants que coincidan con los filtros.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="px-6 py-4 border-t">
                    {{ $tenants->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Tenant -->
    <x-modal name="edit-tenant-modal" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Gestionar Datos del Tenant') }}
            </h2>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="editName" value="{{ __('Nombre del Despacho') }}" />
                    <x-text-input wire:model="editName" id="editName" class="mt-1 block w-full" type="text" />
                    <x-input-error :messages="$errors->get('editName')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="editSlug" value="{{ __('Slug / Subdominio') }}" />
                    <x-text-input wire:model="editSlug" id="editSlug" class="mt-1 block w-full" type="text" />
                    <x-input-error :messages="$errors->get('editSlug')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="editPlan" value="{{ __('Plan Asignado') }}" />
                    <select wire:model="selectedPlanId" id="editPlan" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} - ${{ number_format($plan->price, 2) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('selectedPlanId')" class="mt-2" />
                </div>

                <div class="flex items-center">
                    <input type="checkbox" wire:model="editIsActive" id="editIsActive" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <x-input-label for="editIsActive" value="{{ __('Tenant Activo') }}" class="ml-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close-modal', 'edit-tenant-modal')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" wire:click="updateTenant">
                    {{ __('Guardar Cambios') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal>

    <!-- Modal Cambio de Plan Rápido -->
    <x-modal name="change-plan-modal" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Cambiar Plan (Acceso Rápido)') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Selecciona el nuevo plan para este tenant.
            </p>

            <div class="mt-6">
                <x-input-label for="plan" value="{{ __('Nuevo Plan') }}" />
                <select wire:model="selectedPlanId" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Seleccionar Plan...</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->name }} - ${{ number_format($plan->price, 2) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close-modal', 'change-plan-modal')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" wire:click="changePlan">
                    {{ __('Actualizar Plan') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal>
</div>
