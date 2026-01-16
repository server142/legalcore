<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Facturación y Finanzas') }}
    </h2>
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
            <p class="text-2xl font-bold text-gray-800">$0.00</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-orange-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Pendiente</h3>
            <p class="text-2xl font-bold text-gray-800">$0.00</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Facturas Mes</h3>
            <p class="text-2xl font-bold text-gray-800">0</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Vencidas</h3>
            <p class="text-2xl font-bold text-gray-800">0</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('reportes.factura', $factura) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">PDF</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">No hay facturas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
