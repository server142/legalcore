<div class="p-6">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reporte de Ingresos') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h3 class="text-2xl font-bold text-gray-800">Ingresos por Suscripciones</h3>
                
                <div class="flex items-center gap-2">
                    <input type="date" wire:model.live="startDate" class="rounded-lg border-gray-300 text-sm">
                    <span class="text-gray-500">a</span>
                    <input type="date" wire:model.live="endDate" class="rounded-lg border-gray-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-100">
                    <p class="text-indigo-600 text-sm font-bold uppercase tracking-wider">Total Recaudado</p>
                    <p class="text-4xl font-black text-indigo-900 mt-1">${{ number_format($totalIncome, 2) }} <span class="text-lg font-normal">MXN</span></p>
                </div>
                
                <div class="bg-green-50 p-6 rounded-xl border border-green-100">
                    <p class="text-green-600 text-sm font-bold uppercase tracking-wider">Pagos Completados</p>
                    <p class="text-4xl font-black text-green-900 mt-1">{{ $payments->total() }}</p>
                </div>

                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                    <p class="text-blue-600 text-sm font-bold uppercase tracking-wider">Promedio por Pago</p>
                    <p class="text-4xl font-black text-blue-900 mt-1">
                        ${{ $payments->total() > 0 ? number_format($totalIncome / $payments->total(), 2) : '0.00' }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->payment_date->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                {{ $payment->tenant->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->plan->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                ${{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $payment->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                No se encontraron pagos en el rango seleccionado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
