<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-indigo-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">Finalizar Suscripción</h2>
            </div>
            
            <div class="p-8">
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900">Resumen del Pedido</h3>
                    <div class="mt-4 bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <span class="font-medium text-gray-700">Plan Seleccionado:</span>
                            <span class="font-bold text-indigo-600 text-lg">{{ $plan->name }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="font-medium text-gray-700">Periodo:</span>
                            <span class="text-gray-900">{{ $plan->duration_in_days }} días</span>
                        </div>
                        <div class="border-t border-gray-200 my-4"></div>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-900 text-xl">Total a Pagar:</span>
                            <span class="font-bold text-gray-900 text-2xl">${{ number_format($plan->price, 2) }} MXN</span>
                        </div>
                    </div>
                </div>

                <!-- Stripe Elements Placeholder -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Método de Pago</h3>
                    
                    <div class="bg-white border border-gray-300 rounded-md p-4 mb-4">
                        <div class="flex items-center mb-4">
                            <svg class="h-8 w-8 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span class="font-medium text-gray-700">Tarjeta de Crédito / Débito</span>
                        </div>
                        
                        <!-- Aquí iría el elemento de Stripe -->
                        <div id="card-element" class="p-3 border border-gray-200 rounded bg-gray-50">
                            <!-- Stripe Element will be inserted here -->
                            <p class="text-gray-500 italic text-sm">Formulario de tarjeta seguro (Simulado)</p>
                            <div class="mt-2 flex gap-2">
                                <div class="h-8 w-12 bg-gray-200 rounded"></div>
                                <div class="h-8 w-12 bg-gray-200 rounded"></div>
                                <div class="h-8 w-12 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-500 mb-6">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Tus pagos son procesados de forma segura por Stripe. LegalCore no almacena los datos de tu tarjeta.
                    </p>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('welcome') }}" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 font-medium hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button wire:click="processPayment" wire:loading.attr="disabled" class="px-8 py-3 bg-indigo-600 rounded-md text-white font-bold hover:bg-indigo-700 transition shadow-lg flex items-center">
                        <span wire:loading.remove>Pagar y Suscribirse</span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Procesando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-8 text-gray-500 text-sm">
            &copy; {{ date('Y') }} LegalCore. Todos los derechos reservados.
        </div>
    </div>
</div>
