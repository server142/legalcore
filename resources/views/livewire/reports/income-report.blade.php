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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-100">
                    <p class="text-indigo-600 text-sm font-bold uppercase tracking-wider">Total Cobrado</p>
                    <p class="text-4xl font-black text-indigo-900 mt-1">${{ number_format($totalIncome, 2) }} <span class="text-lg font-normal">MXN</span></p>
                </div>

                <div class="bg-green-50 p-6 rounded-xl border border-green-100">
                    <p class="text-green-600 text-sm font-bold uppercase tracking-wider">Facturas Pagadas</p>
                    <p class="text-4xl font-black text-green-900 mt-1">{{ $facturas->total() }}</p>
                </div>

                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                    <p class="text-blue-600 text-sm font-bold uppercase tracking-wider">Ticket Promedio</p>
                    <p class="text-4xl font-black text-blue-900 mt-1">
                        ${{ $facturas->total() > 0 ? number_format($totalIncome / $facturas->total(), 2) : '0.00' }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <!-- Desktop Table -->
                <div class="income-table-desktop overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Pago</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Concepto</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Recibo</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($facturas as $factura)
                                @php
                                    $fecha = $factura->fecha_pago ?? $factura->fecha_emision ?? $factura->created_at;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ optional($fecha)->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $factura->cliente->nombre ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ data_get($factura->conceptos, '0.descripcion') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-right">
                                        ${{ number_format($factura->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        <a href="{{ route('reportes.factura', $factura) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 font-bold">PDF</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        No hay ingresos en el periodo seleccionado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="income-cards-mobile divide-y divide-gray-200">
                    @forelse($facturas as $factura)
                        @php
                            $fecha = $factura->fecha_pago ?? $factura->fecha_emision ?? $factura->created_at;
                            $concepto = data_get($factura->conceptos, '0.descripcion') ?? '—';
                        @endphp
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-xs font-bold text-gray-500 uppercase">{{ optional($fecha)->format('d/m/Y H:i') ?? '—' }}</div>
                                    <div class="mt-1 text-sm font-bold text-gray-900">{{ $factura->cliente->nombre ?? '—' }}</div>
                                    <div class="mt-1 text-xs text-gray-600">{{ $concepto }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-extrabold text-gray-900">${{ number_format($factura->total, 2) }}</div>
                                    <a href="{{ route('reportes.factura', $factura) }}" target="_blank" class="mt-2 inline-block text-xs font-bold text-indigo-600 hover:text-indigo-900 underline">PDF</a>
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
                {{ $facturas->links() }}
            </div>
        </div>
    </div>
</div>
