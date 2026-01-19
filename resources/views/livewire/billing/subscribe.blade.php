<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ url()->previous() == url()->current() ? route('dashboard') : url()->previous() }}" class="inline-flex items-center text-gray-600 hover:text-indigo-600 transition mb-6 group">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-medium">Regresar</span>
        </a>

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

                <!-- Payment Method Section -->
                @if(!$isFree)
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Método de Pago</h3>
                    
                    <div class="bg-white border border-gray-300 rounded-md p-6 mb-4">
                        <div class="flex items-center mb-6">
                            <svg class="h-8 w-8 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span class="font-medium text-gray-700">Tarjeta de Crédito / Débito</span>
                            <div class="ml-auto flex space-x-2">
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-500">Visa</span>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-500">Mastercard</span>
                            </div>
                        </div>
                        
                        <!-- Simulated Payment Form -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre en la Tarjeta</label>
                                <input type="text" placeholder="Como aparece en la tarjeta" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Tarjeta</label>
                                <div class="relative">
                                    <input type="text" placeholder="0000 0000 0000 0000" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-10">
                                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Expiración</label>
                                    <input type="text" placeholder="MM / AA" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CVC</label>
                                    <div class="relative">
                                        <input type="text" placeholder="123" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <svg class="w-5 h-5 text-gray-400 absolute right-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-500 mb-6 flex items-start">
                        <svg class="w-4 h-4 mt-0.5 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Tus pagos son procesados de forma segura con encriptación SSL de 256-bits.</span>
                    </p>
                </div>
                @else
                <div class="mb-8 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm leading-5 font-medium text-green-800">
                                No se requiere pago
                            </h3>
                            <div class="mt-2 text-sm leading-5 text-green-700">
                                <p>
                                    Estás activando una prueba gratuita. No se te cobrará nada hoy.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('welcome') }}" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 font-medium hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button wire:click="processPayment" wire:loading.attr="disabled" class="px-8 py-3 bg-indigo-600 rounded-md text-white font-bold hover:bg-indigo-700 transition shadow-lg flex items-center">
                        <span wire:loading.remove>{{ $isFree ? 'Iniciar Prueba Gratuita' : 'Pagar y Suscribirse' }}</span>
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
            &copy; {{ date('Y') }} Diogenes. Todos los derechos reservados.
        </div>
    </div>
</div>
