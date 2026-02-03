<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Paquetes (Planes)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <div class="w-1/3">
                            <x-text-input wire:model.live="search" placeholder="Buscar planes..." class="w-full" />
                        </div>
                        <a href="{{ route('admin.plans.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Nuevo Plan') }}
                        </a>
                    </div>

                    @if (session()->has('message'))
                        <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-md">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <!-- Desktop Table -->
                        <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuarios</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($plans as $plan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $plan->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $plan->slug }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">${{ number_format($plan->price, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $plan->duration_in_days }} días</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs text-gray-500">
                                                Admin: {{ $plan->max_admin_users }}<br>
                                                Abogados: {{ $plan->max_lawyer_users ?? 'Ilimitado' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button wire:click="toggleStatus({{ $plan->id }})" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $plan->is_active ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                             <a href="{{ route('admin.plans.edit', $plan) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                             <button wire:click="openFeaturesModal({{ $plan->id }})" class="text-amber-600 hover:text-amber-900 mr-3">Características</button>
                                             <button wire:click="delete({{ $plan->id }})" wire:confirm="¿Estás seguro de eliminar este plan?" class="text-red-600 hover:text-red-900">Eliminar</button>
                                         </td>
                                     </tr>
                                 @empty
                                     <tr>
                                         <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                             No hay planes registrados.
                                         </td>
                                     </tr>
                                 @endforelse
                             </tbody>
                         </table>
 
                         <!-- Mobile Cards -->
                         <div class="block md:hidden">
                             @forelse ($plans as $plan)
                             <div class="p-4 border-b border-gray-200 hover:bg-gray-50 transition">
                                 <div class="flex justify-between items-start mb-2">
                                     <div>
                                         <h3 class="text-lg font-bold text-gray-900">{{ $plan->name }}</h3>
                                         <p class="text-xs text-gray-500">{{ $plan->slug }}</p>
                                     </div>
                                     <div class="text-right">
                                         <div class="text-lg font-bold text-indigo-600">${{ number_format($plan->price, 2) }}</div>
                                         <div class="text-xs text-gray-500">{{ $plan->duration_in_days }} días</div>
                                     </div>
                                 </div>
                                 
                                 <div class="flex justify-between items-center mb-3">
                                     <div class="text-xs text-gray-600">
                                         <span class="font-semibold">Usuarios:</span> {{ $plan->max_admin_users }} Admin, {{ $plan->max_lawyer_users ?? '∞' }} Abogados
                                     </div>
                                     <button wire:click="toggleStatus({{ $plan->id }})" class="px-2 py-1 text-xs font-semibold rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                         {{ $plan->is_active ? 'Activo' : 'Inactivo' }}
                                     </button>
                                 </div>
 
                                 <div class="flex justify-end space-x-3 border-t pt-3">
                                     <a href="{{ route('admin.plans.edit', $plan) }}" wire:navigate class="text-indigo-600 font-medium text-sm hover:text-indigo-800">Editar</a>
                                     <button wire:click="openFeaturesModal({{ $plan->id }})" class="text-amber-600 font-medium text-sm hover:text-amber-800">Características</button>
                                     <button wire:click="delete({{ $plan->id }})" wire:confirm="¿Estás seguro de eliminar este plan?" class="text-red-600 font-medium text-sm hover:text-red-800">Eliminar</button>
                                 </div>
                             </div>
                             @empty
                                 <div class="p-6 text-center text-gray-500">
                                     No hay planes registrados.
                                 </div>
                             @endforelse
                         </div>
                     </div>
 
                     <div class="mt-4">
                         {{ $plans->links() }}
                     </div>
                 </div>
             </div>
         </div>
     </div>
 
     <!-- Modal de Características -->
     @if($showFeaturesModal)
     <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
         <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
             <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
 
             <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
 
             <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                 <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                     <div class="sm:flex sm:items-start">
                         <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                             <h3 class="text-lg leading-6 font-black text-gray-900 uppercase tracking-wider mb-4" id="modal-title">
                                 Editar Características
                             </h3>
                             
                             <div class="mt-4">
                                 <div class="flex gap-2 mb-6">
                                     <input type="text" wire:model="newFeature" wire:keydown.enter.prevent="addFeature" placeholder="Nueva característica..." class="flex-1 rounded-xl border-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                     <button type="button" wire:click="addFeature" class="px-4 py-2 bg-indigo-600 text-white text-xs font-black uppercase rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-100">
                                         Añadir
                                     </button>
                                 </div>
 
                                 <div class="max-h-[300px] overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                                     @foreach($features as $index => $feature)
                                         <div class="flex items-center justify-between bg-gray-50 p-4 rounded-2xl border border-gray-100 group">
                                             <span class="text-sm font-bold text-gray-700">{{ $feature }}</span>
                                             <button type="button" wire:click="removeFeature({{ $index }})" class="text-gray-400 hover:text-red-500 transition-colors">
                                                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                             </button>
                                         </div>
                                     @endforeach
                                     
                                     @if(empty($features))
                                         <div class="text-center py-8">
                                             <span class="text-xs font-bold text-gray-400 uppercase">No hay características extras</span>
                                         </div>
                                     @endif
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3 mt-4">
                     <button type="button" wire:click="saveFeatures" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg shadow-indigo-100 px-6 py-2.5 bg-indigo-600 text-xs font-black text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto transition-all">
                         Guardar Cambios
                     </button>
                     <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-200 px-6 py-2.5 bg-white text-xs font-black text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto transition-all">
                         Cancelar
                     </button>
                 </div>
             </div>
         </div>
     </div>
     @endif
 </div>
