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

            <!-- Stats Cards (Modern Premium Layout) -->
            <div class="flex flex-col lg:flex-row gap-6 mb-10 w-full overflow-x-auto pb-2">
                <!-- Total Tenants -->
                <div class="flex-1 bg-white min-h-[130px] p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] hover:shadow-xl transition-all duration-500 group">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">Total Tenants</span>
                        <div class="p-2 bg-slate-50 rounded-xl group-hover:bg-slate-100 transition-colors">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-4xl font-bold text-slate-900 tracking-tight">{{ \App\Models\Tenant::count() }}</span>
                        <span class="text-xs font-medium text-slate-400">registrados</span>
                    </div>
                </div>

                <!-- En Trial -->
                <div class="flex-1 bg-white min-h-[130px] p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] hover:shadow-xl transition-all duration-500 group">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[11px] font-bold text-blue-500 uppercase tracking-[0.2em]">En Trial</span>
                        <div class="p-2 bg-blue-50 rounded-xl group-hover:bg-blue-100 transition-colors">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-4xl font-bold text-slate-900 tracking-tight">{{ \App\Models\Tenant::where('plan', 'trial')->count() }}</span>
                        <span class="text-xs font-medium text-blue-400">en prueba</span>
                    </div>
                    <div class="mt-4 w-full bg-slate-100 h-1 rounded-full overflow-hidden">
                        <div class="bg-blue-500 h-full w-1/3 opacity-30"></div>
                    </div>
                </div>

                <!-- Activos Pagados -->
                <div class="flex-1 bg-white min-h-[130px] p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] hover:shadow-xl transition-all duration-500 group">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[11px] font-bold text-emerald-500 uppercase tracking-[0.2em]">Activos Pagados</span>
                        <div class="p-2 bg-emerald-50 rounded-xl group-hover:bg-emerald-100 transition-colors">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-4xl font-bold text-slate-900 tracking-tight">{{ \App\Models\Tenant::where('plan', '!=', 'trial')->where('is_active', true)->count() }}</span>
                        <span class="text-xs font-medium text-emerald-400">premium</span>
                    </div>
                </div>

                <!-- Trials Expirados -->
                <div class="flex-1 bg-white min-h-[130px] p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] hover:shadow-xl transition-all duration-500 group">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[11px] font-bold text-rose-500 uppercase tracking-[0.2em]">Trials Expirados</span>
                        <div class="p-2 bg-rose-50 rounded-xl group-hover:bg-rose-100 transition-colors">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-4xl font-bold text-slate-900 tracking-tight">{{ \App\Models\Tenant::where('plan', 'trial')->where('trial_ends_at', '<', now())->count() }}</span>
                        <span class="text-xs font-medium text-rose-400">por vencer</span>
                    </div>
                </div>

                <!-- Activos Hoy -->
                <div class="flex-1 bg-white min-h-[130px] p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] hover:shadow-xl transition-all duration-500 group relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[11px] font-bold text-indigo-500 uppercase tracking-[0.2em]">Activos Hoy</span>
                        <div class="p-2 bg-indigo-50 rounded-xl group-hover:bg-indigo-100 transition-colors">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-4xl font-bold text-slate-900 tracking-tight">{{ \App\Models\AuditLog::withoutGlobalScopes()->where('created_at', '>=', now()->startOfDay())->distinct('tenant_id')->count('tenant_id') }}</span>
                        <span class="text-xs font-medium text-indigo-400">actividad</span>
                    </div>
                    <!-- Subtle background pulse -->
                    <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                </div>
            </div>

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
                            <option value="churn">Churn (15 días sin actividad)</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button wire:click="exportCSV" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Exportar Excel (CSV)
                        </button>
                    </div>
                </div>
            </div>


            <!-- Tenants Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <!-- Desktop Table -->
                    <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant / Alta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan / Uso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento / Actividad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tenants as $tenant)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $tenant->name }}</div>
                                        <div class="text-[10px] text-gray-500 uppercase tracking-tighter">{{ $tenant->slug }}</div>
                                        <div class="text-[9px] text-indigo-500 mt-1 uppercase font-semibold italic">Alta: {{ $tenant->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="px-2 py-0.5 inline-flex text-[10px] items-center justify-center font-bold rounded-full w-fit mb-1
                                                {{ $tenant->plan === 'trial' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ $tenant->planRelation ? $tenant->planRelation->name : ucfirst($tenant->plan) }}
                                            </span>
                                            <div class="flex flex-col space-y-2 mt-3 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                                <div class="flex items-center text-xs" title="Usuarios totales">
                                                    <div class="w-6 h-6 flex items-center justify-center bg-white rounded-lg border border-slate-200 mr-2 shadow-sm">
                                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                                    </div>
                                                    <span class="text-slate-800 font-bold">{{ $tenant->users->count() }}</span>
                                                    <span class="text-slate-500 ml-1.5">Usuarios</span>
                                                </div>
                                                <div class="flex items-center text-xs" title="Expedientes totales">
                                                    <div class="w-6 h-6 flex items-center justify-center bg-white rounded-lg border border-slate-200 mr-2 shadow-sm">
                                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                                    </div>
                                                    <span class="text-slate-800 font-bold">{{ $tenant->expedientes_count }}</span>
                                                    <span class="text-slate-500 ml-1.5">Expedientes</span>
                                                </div>
                                                <div class="flex items-center text-xs" title="Almacenamiento usado">
                                                    <div class="w-6 h-6 flex items-center justify-center bg-white rounded-lg border border-slate-200 mr-2 shadow-sm">
                                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 1.1.9 2 2 2h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2zm0 0h16M12 11h4m-4 4h4"></path></svg>
                                                    </div>
                                                    <span class="text-slate-800 font-bold">{{ $tenant->storage_usage_formatted }}</span>
                                                    <span class="text-slate-500 ml-1.5">Disco</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                                        @if($tenant->expiration_date)
                                            <div class="flex items-center {{ $tenant->is_expired ? 'text-red-600 font-bold' : 'text-slate-900 font-semibold' }}">
                                                <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                {{ $tenant->expiration_date->format('d/m/Y') }}
                                            </div>
                                            @if(!$tenant->is_expired)
                                                <div class="text-[10px] text-green-600 font-bold mt-1 bg-green-50 px-2 py-0.5 rounded-full inline-block">
                                                    {{ round(now()->diffInDays($tenant->expiration_date, false)) }} días restantes
                                                </div>
                                            @endif
                                        @endif
                                        
                                        <div class="mt-2 pt-2 border-t border-gray-100">
                                            @if($tenant->last_activity)
                                                <div class="text-[9px] text-gray-500 uppercase font-black">Última Actividad:</div>
                                                <div class="flex items-center">
                                                    <span class="h-1.5 w-1.5 rounded-full mr-1.5 {{ $tenant->last_activity->diffInDays() < 3 ? 'bg-green-500' : ($tenant->last_activity->diffInDays() < 7 ? 'bg-orange-400' : 'bg-red-500') }}"></span>
                                                    <div class="text-[10px] font-mono {{ $tenant->last_activity->diffInDays() < 7 ? 'text-indigo-600' : 'text-red-600 font-bold' }}">
                                                        {{ $tenant->last_activity->diffForHumans() }}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-[9px] text-gray-400 italic">Sin actividad registrada</div>
                                            @endif
                                        </div>
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

                            <div class="mb-4 text-xs">
                                <div class="grid grid-cols-2 gap-2 mb-3 bg-gray-50 p-2 rounded border">
                                    <div>
                                        <span class="text-[9px] font-black text-gray-400 uppercase block">Alta</span>
                                        <span class="text-gray-700 font-bold">{{ $tenant->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-black text-gray-400 uppercase block">Expedientes</span>
                                        <span class="text-indigo-600 font-bold">{{ $tenant->expedientes_count }}</span>
                                    </div>
                                    <div class="col-span-2 mt-1 pt-1 border-t grid grid-cols-2 gap-2">
                                        <div>
                                            <span class="text-[9px] font-black text-gray-400 uppercase block">Última Actividad</span>
                                            <span class="text-emerald-600 font-bold">
                                                {{ $tenant->last_activity ? $tenant->last_activity->diffForHumans() : 'Sin actividad' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-black text-gray-400 uppercase block">Almacenamiento</span>
                                            <span class="text-amber-600 font-bold">{{ $tenant->storage_usage_formatted }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($tenant->expiration_date)
                                    <div class="flex items-center">
                                        <span class="text-[9px] font-black text-gray-400 uppercase mr-2">Vencimiento:</span>
                                        <span class="{{ $tenant->is_expired ? 'text-red-600 font-bold' : 'text-gray-900 font-bold' }}">
                                            {{ $tenant->expiration_date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    @if(!$tenant->is_expired)
                                        <div class="text-[10px] text-green-600 font-bold mt-0.5">{{ now()->diffInDays($tenant->expiration_date) }} días restantes</div>
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
