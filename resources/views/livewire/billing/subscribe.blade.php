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
                        
                        <!-- Stripe Elements Form -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre en la Tarjeta</label>
                                <input type="text" id="card-holder-name" placeholder="Como aparece en la tarjeta" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Datos de la Tarjeta</label>
                                <div id="card-element" class="w-full border border-gray-300 rounded-md p-3 bg-white shadow-sm focus-within:ring-1 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                                    <!-- Stripe Element se montará aquí -->
                                </div>
                                <div id="card-errors" class="mt-2 text-sm text-red-600" role="alert"></div>
                            </div>
                        </div>
                    </div>

                    @error('payment')
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-md text-sm">
                            {{ $message }}
                        </div>
                    @enderror

                    <p class="text-sm text-gray-500 mb-6 flex items-start">
                        <svg class="w-4 h-4 mt-0.5 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Tus pagos son procesados de forma segura por Stripe con encriptación SSL.</span>
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
                    
                    @if(!$isFree)
                        <button id="card-button" data-secret="{{ $clientSecret }}" class="px-8 py-3 bg-indigo-600 rounded-md text-white font-bold hover:bg-indigo-700 transition shadow-lg flex items-center">
                            <span id="button-text">Pagar y Suscribirse</span>
                            <span id="button-spinner" class="hidden flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Procesando...
                            </span>
                        </button>
                    @else
                        <button wire:click="processPayment" wire:loading.attr="disabled" class="px-8 py-3 bg-indigo-600 rounded-md text-white font-bold hover:bg-indigo-700 transition shadow-lg flex items-center">
                            <span wire:loading.remove>Iniciar Prueba Gratuita</span>
                            <span wire:loading class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Procesando...
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="text-center mt-8 text-gray-500 text-sm">
            &copy; {{ date('Y') }} Diogenes. Todos los derechos reservados.
        </div>
    </div>

    @if(!$isFree)
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stripe = Stripe('{{ config('cashier.key') }}');
            const elements = stripe.elements();
            const cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#32325d',
                    },
                },
            });

            cardElement.mount('#card-element');

            const cardHolderName = document.getElementById('card-holder-name');
            const cardButton = document.getElementById('card-button');
            const clientSecret = cardButton.dataset.secret;
            const cardErrors = document.getElementById('card-errors');
            const buttonText = document.getElementById('button-text');
            const buttonSpinner = document.getElementById('button-spinner');

            cardButton.addEventListener('click', async (e) => {
                e.preventDefault();
                
                // UI Loading state
                cardButton.disabled = true;
                buttonText.classList.add('hidden');
                buttonSpinner.classList.remove('hidden');
                cardErrors.textContent = '';

                const { setupIntent, error } = await stripe.confirmCardSetup(
                    clientSecret, {
                        payment_method: {
                            card: cardElement,
                            billing_details: { name: cardHolderName.value }
                        }
                    }
                );

                if (error) {
                    // Error handling
                    cardErrors.textContent = error.message;
                    cardButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    buttonSpinner.classList.add('hidden');
                } else {
                    // Success - Send to Livewire
                    @this.call('processPayment', setupIntent.payment_method);
                }
            });
        });
    </script>
    @endif
</div>
