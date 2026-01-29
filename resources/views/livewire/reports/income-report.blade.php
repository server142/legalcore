<div class="p-6">
    <style>
        .income-table-desktop { display: none; }
        .income-cards-mobile { display: block; }
        @media (min-width: 768px) {
            .income-table-desktop { display: block; }
            .income-cards-mobile { display: none; }
        }
    </style>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reporte de Ingresos') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h3 class="text-2xl font-bold text-gray-800">Ingresos (por fecha de pago)</h3>

                <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full md:w-auto">
                    <div class="flex items-center gap-2">
                        <input type="date" wire:model.defer="startDate" class="rounded-lg border-gray-300 text-sm">
                        <span class="text-gray-500">a</span>
                        <input type="date" wire:model.defer="endDate" class="rounded-lg border-gray-300 text-sm">
                    </div>
                    <button type="button" wire:click="consultar" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
                        Consultar
                    </button>
                    <button type="button" wire:click="exportarCsv" class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-bold hover:bg-green-700">
                        Exportar (CSV)
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-100">
                    <p class="text-indigo-600 text-sm font-bold uppercase tracking-wider">Total Cobrado</p>
                    <p class="text-4xl font-black text-indigo-900 mt-1">${{ number_format($totalIncome, 2) }} <span class="text-lg font-normal">MXN</span></p>
                </div>

                <div class="bg-green-50 p-6 rounded-xl border border-green-100">
                    <p class="text-green-600 text-sm font-bold uppercase tracking-wider">Facturas Pagadas</p>
                    <p class="text-2xl font-black text-green-900 mt-1">${{ number_format($totalFacturas, 2) }}</p>
                </div>

                <div class="bg-yellow-50 p-6 rounded-xl border border-yellow-100">
                    <p class="text-yellow-600 text-sm font-bold uppercase tracking-wider">Anticipos</p>
                    <p class="text-2xl font-black text-yellow-900 mt-1">${{ number_format($totalAnticipos, 2) }}</p>
                </div>

                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                    <p class="text-blue-600 text-sm font-bold uppercase tracking-wider">Transacciones</p>
                    <p class="text-2xl font-black text-blue-900 mt-1">{{ $total }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <!-- Desktop Table -->
                <div class="income-table-desktop overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Pago</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Concepto</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Referencia</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($ingresos as $ingreso)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ optional($ingreso->fecha)->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ingreso->tipo == 'factura')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Factura</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Anticipo</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $ingreso->cliente }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $ingreso->concepto }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-right">
                                        ${{ number_format($ingreso->monto, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        {{ $ingreso->referencia }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        No hay ingresos en el periodo seleccionado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="income-cards-mobile divide-y divide-gray-200">
                    @forelse($ingresos as $ingreso)
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-xs font-bold text-gray-500 uppercase">{{ optional($ingreso->fecha)->format('d/m/Y H:i') ?? '—' }}</div>
                                        @if($ingreso->tipo == 'factura')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Factura</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Anticipo</span>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-sm font-bold text-gray-900">{{ $ingreso->cliente }}</div>
                                    <div class="mt-1 text-xs text-gray-600">{{ $ingreso->concepto }}</div>
                                    <div class="mt-1 text-xs text-gray-500">Ref: {{ $ingreso->referencia }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-extrabold text-gray-900">${{ number_format($ingreso->monto, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-gray-500">
                            No hay ingresos en el periodo seleccionado.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">
                {{-- Paginación manual --}}
            </div>
        </div>
    </div>
</div>
