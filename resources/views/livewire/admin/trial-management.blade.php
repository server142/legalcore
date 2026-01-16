<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gestión de Trials y Suscripciones') }}
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
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input wire:model.live="search" type="text" placeholder="Buscar por nombre..." class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <select wire:model.live="filterStatus" class="w-full rounded-lg border-gray-300">
                        <option value="all">Todos</option>
                        <option value="trial">En Trial</option>
                        <option value="active">Suscripciones Activas</option>
                        <option value="expired">Trials Expirados</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <div class="text-sm text-gray-500 mb-1">Total Tenants</div>
                <div class="text-3xl font-bold text-gray-900">{{ \App\Models\Tenant::count() }}</div>
            </div>
            <div class="bg-blue-50 p-6 rounded-xl shadow-sm">
                <div class="text-sm text-blue-600 mb-1">En Trial</div>
                <div class="text-3xl font-bold text-blue-900">{{ \App\Models\Tenant::where('plan', 'trial')->count() }}</div>
            </div>
            <div class="bg-green-50 p-6 rounded-xl shadow-sm">
                <div class="text-sm text-green-600 mb-1">Suscripciones Activas</div>
                <div class="text-3xl font-bold text-green-900">{{ \App\Models\Tenant::where('plan', '!=', 'trial')->where('is_active', true)->count() }}</div>
            </div>
            <div class="bg-red-50 p-6 rounded-xl shadow-sm">
                <div class="text-sm text-red-600 mb-1">Trials Expirados</div>
                <div class="text-3xl font-bold text-red-900">{{ \App\Models\Tenant::where('plan', 'trial')->where('trial_ends_at', '<', now())->count() }}</div>
            </div>
        </div>

        <!-- Tenants Table -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Trial Expira</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Usuarios</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tenants as $tenant)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $tenant->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $tenant->domain }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $tenant->plan === 'trial' ? 'bg-blue-100 text-blue-800' : 
                                           ($tenant->plan === 'basico' ? 'bg-gray-100 text-gray-800' : 
                                           ($tenant->plan === 'profesional' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800')) }}">
                                        {{ ucfirst($tenant->plan) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($tenant->trial_ends_at)
                                        <div class="{{ $tenant->trialExpired() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ $tenant->trial_ends_at->format('d/m/Y') }}
                                        </div>
                                        @if($tenant->isOnTrial())
                                            <div class="text-xs text-gray-500">{{ $tenant->daysLeftInTrial() }} días restantes</div>
                                        @elseif($tenant->trialExpired())
                                            <div class="text-xs text-red-500">Expirado</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $tenant->users->count() }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $tenant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $tenant->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex space-x-2">
                                        @if($tenant->plan === 'trial')
                                            <button wire:click="extendTrial({{ $tenant->id }}, 30)" class="text-blue-600 hover:text-blue-800 font-medium">
                                                +30 días
                                            </button>
                                            <button wire:click="convertToPaid({{ $tenant->id }}, 'profesional')" class="text-green-600 hover:text-green-800 font-medium">
                                                Convertir
                                            </button>
                                        @endif
                                        @if($tenant->is_active)
                                            <button wire:click="deactivateTenant({{ $tenant->id }})" wire:confirm="¿Desactivar este tenant?" class="text-red-600 hover:text-red-800 font-medium">
                                                Desactivar
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No se encontraron tenants.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t">
                {{ $tenants->links() }}
            </div>
        </div>
    </div>
</div>
