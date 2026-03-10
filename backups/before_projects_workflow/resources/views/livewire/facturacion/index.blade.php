<x-slot name="header">
    <x-header title="{{ __('Facturación y Finanzas') }}" subtitle="Control de ingresos y recibos" />
</x-slot>

<div class="p-6">
    <div class="flex justify-end items-center mb-6">
        <x-primary-button wire:click="create">
            {{ __('+ Nueva Factura') }}
        </x-primary-button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Total Cobrado</h3>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($totalCobrado, 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-orange-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Pendiente</h3>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($totalPendiente, 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Facturas Mes</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $facturasMes }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Vencidas</h3>
            <p class="text-2xl font-bold text-gray-800">{{ $facturasVencidas }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Desktop Table -->
        <table class="min-w-full divide-y divide-gray-200 hidden md:table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($facturas as $factura)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $factura->cliente->nombre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $factura->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">${{ number_format($factura->total, 2) }} {{ $factura->moneda }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $factura->estado == 'pagada' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                            {{ ucfirst($factura->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                        <a href="{{ route('reportes.factura', $factura) }}" target="_blank" title="Imprimir" class="p-1.5 bg-indigo-50 text-indigo-700 rounded-md hover:bg-indigo-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </a>
                        @if($factura->estado == 'pendiente')
                            <button wire:click="markAsPaid({{ $factura->id }})" wire:confirm="¿Estás seguro de marcar esta factura como PAGADA?" title="Marcar como Pagada" class="p-1.5 bg-green-50 text-green-700 rounded-md hover:bg-green-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                            <button wire:click="markAsCancelled({{ $factura->id }})" wire:confirm="¿Estás seguro de CANCELAR esta factura?" title="Cancelar Factura" class="p-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">No hay facturas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Mobile Cards -->
        <div class="block md:hidden">
            @forelse($facturas as $factura)
            <div class="p-4 border-b border-gray-200 hover:bg-gray-50 transition">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $factura->cliente->nombre }}</h3>
                        <p class="text-xs text-gray-500">{{ $factura->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-900">${{ number_format($factura->total, 2) }} <span class="text-xs font-normal text-gray-500">{{ $factura->moneda }}</span></div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $factura->estado == 'pagada' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                            {{ ucfirst($factura->estado) }}
                        </span>
                    </div>
                </div>
                
                <div class="flex justify-end mt-3 pt-3 border-t border-gray-100 space-x-4">
                    <a href="{{ route('reportes.factura', $factura) }}" target="_blank" class="flex items-center text-indigo-600 font-bold text-xs hover:text-indigo-800 transition">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Imprimir
                    </a>
                    @if($factura->estado == 'pendiente')
                        <button wire:click="markAsPaid({{ $factura->id }})" wire:confirm="¿Marcar como PAGADA?" class="flex items-center text-green-600 font-bold text-xs hover:text-green-800 transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Pagar
                        </button>
                        <button wire:click="markAsCancelled({{ $factura->id }})" wire:confirm="¿CANCELAR factura?" class="flex items-center text-red-600 font-bold text-xs hover:text-red-800 transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Cancelar
                        </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500">No hay facturas registradas.</div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <x-modal-wire wire:model="showModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Nueva Factura') }}
            </h2>

            <div class="mt-6 space-y-6">
                <!-- Cliente -->
                <div>
                    <x-input-label for="cliente_id" :value="__('Cliente')" />
                    <select wire:model="cliente_id" id="cliente_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">-- Seleccionar Cliente --</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                </div>

                <!-- Concepto -->
                <div>
                    <x-input-label for="concepto" :value="__('Concepto')" />
                    <textarea wire:model="concepto" id="concepto" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2"></textarea>
                    <x-input-error :messages="$errors->get('concepto')" class="mt-2" />
                </div>

                <!-- Total -->
                <div>
                    <x-input-label for="total" :value="__('Total')" />
                    <x-text-input wire:model="total" id="total" class="block mt-1 w-full" type="number" step="0.01" required />
                    <x-input-error :messages="$errors->get('total')" class="mt-2" />
                </div>

                <!-- Moneda -->
                <div>
                    <x-input-label for="moneda" :value="__('Moneda')" />
                    <select wire:model="moneda" id="moneda" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="MXN">MXN</option>
                        <option value="USD">USD</option>
                    </select>
                    <x-input-error :messages="$errors->get('moneda')" class="mt-2" />
                </div>

                <!-- Estado -->
                <div>
                    <x-input-label for="estado" :value="__('Estado')" />
                    <select wire:model="estado" id="estado" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="pendiente">Pendiente</option>
                        <option value="pagada">Pagada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                    <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('showModal', false)">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" wire:click="store">
                    {{ __('Guardar') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal-wire>
</div>
