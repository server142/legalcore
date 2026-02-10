<div class="min-h-screen bg-gray-50 py-12">
    
    @if($showPricing)
        <!-- PRICING TABLE MODE -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Planes diseñados para abogados modernos
                </h2>
                <p class="mt-4 text-xl text-gray-500">
                    Elige la potencia que tu despacho necesita. Sin contratos forzosos.
                </p>
            </div>

            <div class="mt-16 bg-white rounded-2xl shadow-xl overflow-hidden mb-12">
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y divide-gray-200 md:divide-y-0 md:divide-x divide-gray-200">
                    @foreach($availablePlans as $p)
                        <div class="p-8 flex flex-col justify-between hover:bg-gray-50 transition-colors relative group">
                            @if($p->slug === 'pro' || $p->slug === 'avanzado') 
                                <div class="absolute top-0 right-0 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg uppercase tracking-wider shadow-sm">más popular</div>
                            @endif
                            
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 uppercase tracking-widest">{{ $p->name }}</h3>
                                <div class="mt-4 flex items-baseline text-gray-900">
                                    <span class="text-4xl font-extrabold tracking-tight">${{ number_format($p->price, 0) }}</span>
                                    <span class="ml-1 text-xl font-semibold text-gray-500">/{{ $p->duration_in_days == 30 ? 'mes' : 'año' }}</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">MXN + IVA</p>

                                <ul role="list" class="mt-6 space-y-4">
                                    <!-- Explicit Counts matching Landing Page -->
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700 leading-snug">{{ $p->max_admin_users }} Admin</p>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <p class="ml-3 text-sm text-gray-700 leading-snug">{{ $p->max_lawyer_users ?? 'Ilimitados' }} Abogados</p>
                                    </li>

                                    @php $features = is_string($p->features) ? json_decode($p->features, true) : $p->features; @endphp
                                    @foreach($features ?? [] as $feature)
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700 leading-snug">{{ $feature }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            @php
                                $btnColors = [
                                    0 => 'bg-indigo-600 hover:bg-indigo-700 text-white',
                                    1 => 'bg-emerald-600 hover:bg-emerald-700 text-white',
                                    2 => 'bg-purple-600 hover:bg-purple-700 text-white',
                                ];
                                $btnClass = $btnColors[$loop->index % 3];
                            @endphp

                            <a href="{{ route('billing.subscribe', $p->slug) }}" class="mt-8 block w-full rounded-xl py-4 px-6 text-center text-sm font-bold transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 {{ $btnClass }}">
                                Seleccionar {{ $p->name }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            
             <div class="text-center mt-8">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-900 font-medium">← Volver al Dashboard</a>
            </div>
        </div>

    @else
        <!-- CHECKOUT MODE -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('billing.subscribe', 'trial') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Cambiar de plan
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden lg:grid lg:grid-cols-12 lg:gap-x-8">
                
                <!-- Order Summary (Right on Desktop, Top on Mobile) -->
                <div class="bg-gray-50 lg:col-span-5 p-8 lg:px-12 lg:py-16 border-b lg:border-b-0 lg:border-r border-gray-200 order-first lg:order-last">
                    <div class="lg:sticky lg:top-8">
                        <h2 class="text-2xl font-black text-gray-900 mb-6">Resumen de tu Orden</h2>
                        
                        <div class="flow-root">
                            <ul role="list" class="-my-6 divide-y divide-gray-200">
                                <li class="py-6 flex space-x-6">
                                    <div class="bg-indigo-100 rounded-lg w-16 h-16 flex items-center justify-center text-indigo-600 shrink-0">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <div class="flex-auto space-y-1">
                                        <h3 class="text-gray-900 font-bold uppercase tracking-wider">Plan {{ $plan->name }}</h3>
                                        <p class="text-sm text-gray-500">Suscripción {{ $plan->duration_in_days == 30 ? 'Mensual' : 'Anual' }}</p>
                                    </div>
                                    <p class="flex-none font-bold text-gray-900">${{ number_format($plan->price, 2) }}</p>
                                </li>
                            </ul>
                        </div>

                        <dl class="mt-10 pt-10 border-t border-gray-200 space-y-6 text-sm font-medium text-gray-500">
                            <div class="flex justify-between">
                                <dt>Subtotal</dt>
                                <dd class="text-gray-900">${{ number_format($plan->price, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt>IVA (16%)</dt>
                                <dd class="text-gray-900">${{ number_format($plan->price * 0.16, 2) }}</dd>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 pt-6 text-base">
                                <dt class="font-black text-gray-900">Total a Pagar</dt>
                                <dd class="font-black text-indigo-600 text-xl">${{ number_format($plan->price * 1.16, 2) }}</dd>
                            </div>
                        </dl>

                        <div class="mt-8 bg-indigo-50 rounded-lg p-4 flex items-start">
                            <svg class="h-5 w-5 text-indigo-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="ml-3 text-xs text-indigo-700">
                                Tu suscripción se renovará automáticamente cada {{ $plan->duration_in_days == 30 ? 'mes' : 'año' }}. Puedes cancelar cuando quieras desde tu panel.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment Form (Left on Desktop) -->
                <div class="lg:col-span-7 p-8 lg:px-12 lg:py-16">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">Información de Pago</h2>
                    
                    @if (session()->has('error'))
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form id="payment-form" wire:submit.prevent="processPayment">
                        <!-- Billing Details -->
                        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4 mb-8">
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-bold text-gray-700">Nombre en la tarjeta</label>
                                <div class="mt-1">
                                    <input type="text" id="name" name="name" autocomplete="cc-name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-3" placeholder="Como aparece en el plástico">
                                </div>
                            </div>
                        </div>

                        <!-- Stripe Element -->
                        <div class="mb-8">
                            <label for="card-element" class="block text-sm font-bold text-gray-700 mb-2">Datos de la Tarjeta</label>
                            <div id="card-element" class="p-4 border border-gray-300 rounded-md shadow-sm bg-gray-50">
                                <!-- Stripe Element will be inserted here -->
                            </div>
                            <div id="card-errors" role="alert" class="mt-2 text-sm text-red-600 font-medium"></div>
                        </div>

                        <div class="mt-10">
                            <button type="submit" id="card-button" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-sm text-lg font-black text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5" data-secret="{{ $clientSecret }}">
                                Pagar y Suscribirse
                            </button>
                            <p class="mt-4 text-center text-xs text-gray-500">
                                Transacción segura y cifrada vía Stripe SSL.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Stripe JS -->
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                const stripe = Stripe('{{ config('cashier.key') }}');
                const elements = stripe.elements();
                const cardElement = elements.create('card', {
                    style: {
                        base: {
                            fontSize: '16px',
                            color: '#32325d',
                            fontFamily: '"Outfit", sans-serif',
                            '::placeholder': {
                                color: '#aab7c4',
                            },
                        },
                        invalid: {
                            color: '#fa755a',
                            iconColor: '#fa755a',
                        },
                    },
                    hidePostalCode: true,
                });
                cardElement.mount('#card-element');

                const cardButton = document.getElementById('card-button');
                const clientSecret = cardButton.dataset.secret;
                const cardErrors = document.getElementById('card-errors');

                cardButton.addEventListener('click', async (e) => {
                    e.preventDefault();
                    cardButton.disabled = true;
                    cardButton.innerText = 'Procesando...';

                    const { setupIntent, error } = await stripe.confirmCardSetup(
                        clientSecret, {
                            payment_method: {
                                card: cardElement,
                                billing_details: { name: document.getElementById('name').value }
                            }
                        }
                    );

                    if (error) {
                        cardErrors.textContent = error.message;
                        cardButton.disabled = false;
                        cardButton.innerText = 'Pagar y Suscribirse';
                    } else {
                        @this.processPayment(setupIntent.payment_method);
                    }
                });
            });
        </script>
    @endif
</div>
