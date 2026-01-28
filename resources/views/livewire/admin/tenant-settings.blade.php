<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configuración del Despacho') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form wire:submit="save" class="space-y-6">
                        <!-- Plan Information Section -->
                        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-medium text-indigo-900 mb-4">{{ __('Mi Plan Actual') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <p class="text-sm text-indigo-600 font-semibold uppercase">Plan</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $currentPlanDetails->name ?? 'Prueba Gratuita' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-indigo-600 font-semibold uppercase">Estado</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subscriptionStatus === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($subscriptionStatus ?? 'Trial') }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-indigo-600 font-semibold uppercase">Vencimiento</p>
                                    <p class="text-lg font-medium text-gray-900">
                                        {{ $subscriptionEndsAt ? \Carbon\Carbon::parse($subscriptionEndsAt)->format('d/m/Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-indigo-200 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Límite de Abogados</p>
                                    <p class="text-gray-900">{{ $currentPlanDetails->max_lawyer_users ?? 'Ilimitado' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Límite de Admins</p>
                                    <p class="text-gray-900">{{ $currentPlanDetails->max_admin_users ?? '1' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Upgrade Options -->
                        @if($availablePlans->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Mejorar mi Plan') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach($availablePlans as $plan)
                                <div class="border rounded-lg p-6 hover:shadow-lg transition bg-white flex flex-col">
                                    <div class="flex-grow">
                                        <h4 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h4>
                                        <p class="text-2xl font-bold text-indigo-600 mt-2">${{ number_format($plan->price, 2) }} <span class="text-sm text-gray-500 font-normal">/mes</span></p>
                                        <ul class="mt-4 space-y-2">
                                            <li class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                {{ $plan->max_lawyer_users ?? 'Ilimitados' }} Abogados
                                            </li>
                                            <li class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                {{ $plan->max_admin_users }} Admins
                                            </li>
                                            @if(is_array($plan->features))
                                                @foreach($plan->features as $feature)
                                                <li class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    {{ $feature }}
                                                </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="mt-6">
                                        @php
                                            $colors = [
                                                0 => 'bg-indigo-600 hover:bg-indigo-700',
                                                1 => 'bg-emerald-600 hover:bg-emerald-700',
                                                2 => 'bg-purple-600 hover:bg-purple-700',
                                            ];
                                            $colorClass = $colors[$loop->index % 3];
                                        @endphp
                                        <a href="{{ route('billing.subscribe', $plan->slug) }}" class="block w-full text-center {{ $colorClass }} text-white px-4 py-2 rounded-lg transition">
                                            Actualizar a {{ $plan->name }}
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Logo -->
                            <div class="md:col-span-2 flex items-center space-x-6">
                                <div class="shrink-0">
                                    <div class="h-32 w-32 rounded-lg border overflow-hidden bg-gray-50 flex items-center justify-center">
                                        @if ($logo)
                                            <img class="max-h-full max-w-full object-contain" src="{{ $logo->temporaryUrl() }}">
                                        @elseif ($logo_path)
                                            <img class="max-h-full max-w-full object-contain" src="{{ asset('storage/' . $logo_path) }}">
                                        @else
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>
                                </div>
                                <label class="block">
                                    <span class="sr-only">Elegir logo</span>
                                    <input type="file" wire:model="logo" class="block w-full text-sm text-slate-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100
                                    "/>
                                    <div wire:loading wire:target="logo" class="mt-2 text-sm text-gray-500 italic">Subiendo...</div>
                                    <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                                </label>
                            </div>

                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Nombre del Despacho')" />
                                <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Titular -->
                            <div>
                                <x-input-label for="titular" :value="__('Titular del Despacho')" />
                                <x-text-input wire:model="titular" id="titular" class="block mt-1 w-full" type="text" />
                                <x-input-error :messages="$errors->get('titular')" class="mt-2" />
                            </div>

                            <!-- Dirección -->
                            <div class="md:col-span-2">
                                <x-input-label for="direccion" :value="__('Dirección Física')" />
                                <x-text-input wire:model="direccion" id="direccion" class="block mt-1 w-full" type="text" />
                                <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                            </div>

                            <!-- Titulares Adjuntos -->
                            <div class="md:col-span-2">
                                <x-input-label for="titulares_adjuntos" :value="__('Titulares Adjuntos (separados por coma)')" />
                                <x-text-input wire:model="titulares_adjuntos" id="titulares_adjuntos" class="block mt-1 w-full" type="text" />
                                <x-input-error :messages="$errors->get('titulares_adjuntos')" class="mt-2" />
                            </div>

                            <!-- Datos Generales -->
                            <div class="md:col-span-2">
                                <x-input-label for="datos_generales" :value="__('Datos Generales / Notas')" />
                                <textarea wire:model="datos_generales" id="datos_generales" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                                <x-input-error :messages="$errors->get('datos_generales')" class="mt-2" />
                            </div>

                            <!-- SMS Notifications Section -->
                            <div class="md:col-span-2 pt-6 border-t">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Notificaciones SMS de Términos') }}</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox" wire:model.live="sms_enabled" id="sms_enabled" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <x-input-label for="sms_enabled" :value="__('Activar avisos por SMS')" />
                                    </div>

                                    @if($sms_enabled)
                                        <div>
                                            <x-input-label for="sms_days_before" :value="__('Avisar cuántos días antes')" />
                                            <x-text-input wire:model="sms_days_before" id="sms_days_before" type="number" min="1" max="30" class="block mt-1 w-full" />
                                            <x-input-error :messages="$errors->get('sms_days_before')" class="mt-2" />
                                        </div>

                                        <div class="md:col-span-2">
                                            <x-input-label for="sms_recipients" :value="__('Números de teléfono a notificar (separados por coma)')" />
                                            <x-text-input wire:model="sms_recipients" id="sms_recipients" type="text" placeholder="+521234567890, +520987654321" class="block mt-1 w-full" />
                                            <p class="mt-1 text-xs text-gray-500 italic">Incluye el código de país (ej. +52 para México).</p>
                                            <x-input-error :messages="$errors->get('sms_recipients')" class="mt-2" />
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Asesorías & Agenda Settings -->
                        <div class="md:col-span-2 pt-6 border-t">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Asesorías y Agenda') }}</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="asesorias_working_hours_start" :value="__('Horario laboral (inicio)')" />
                                    <x-text-input wire:model="asesorias_working_hours_start" id="asesorias_working_hours_start" type="time" class="block mt-1 w-full" />
                                    <x-input-error :messages="$errors->get('asesorias_working_hours_start')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="asesorias_working_hours_end" :value="__('Horario laboral (fin)')" />
                                    <x-text-input wire:model="asesorias_working_hours_end" id="asesorias_working_hours_end" type="time" class="block mt-1 w-full" />
                                    <x-input-error :messages="$errors->get('asesorias_working_hours_end')" class="mt-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Días hábiles') }}</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        <label class="inline-flex items-center space-x-2">
                                            <input type="checkbox" value="mon" wire:model="asesorias_business_days" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">{{ __('Lunes') }}</span>
                                        </label>
                                        <label class="inline-flex items-center space-x-2">
                                            <input type="checkbox" value="tue" wire:model="asesorias_business_days" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">{{ __('Martes') }}</span>
                                        </label>
                                        <label class="inline-flex items-center space-x-2">
                                            <input type="checkbox" value="wed" wire:model="asesorias_business_days" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">{{ __('Miércoles') }}</span>
                                        </label>
                                        <label class="inline-flex items-center space-x-2">
                                            <input type="checkbox" value="thu" wire:model="asesorias_business_days" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">{{ __('Jueves') }}</span>
                                        </label>
                                        <label class="inline-flex items-center space-x-2">
                                            <input type="checkbox" value="fri" wire:model="asesorias_business_days" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">{{ __('Viernes') }}</span>
                                        </label>
                                        <label class="inline-flex items-center space-x-2">
                                            <input type="checkbox" value="sat" wire:model="asesorias_business_days" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">{{ __('Sábado') }}</span>
                                        </label>
                                        <label class="inline-flex items-center space-x-2">
                                            <input type="checkbox" value="sun" wire:model="asesorias_business_days" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">{{ __('Domingo') }}</span>
                                        </label>
                                    </div>
                                    <x-input-error :messages="$errors->get('asesorias_business_days')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="asesorias_slot_minutes" :value="__('Granularidad (minutos)')" />
                                    <x-text-input wire:model="asesorias_slot_minutes" id="asesorias_slot_minutes" type="number" min="5" max="60" class="block mt-1 w-full" />
                                    <x-input-error :messages="$errors->get('asesorias_slot_minutes')" class="mt-2" />
                                </div>

                                <div class="flex items-center space-x-3 pt-6">
                                    <input type="checkbox" wire:model.live="asesorias_enforce_availability" id="asesorias_enforce_availability" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <x-input-label for="asesorias_enforce_availability" :value="__('Validar disponibilidad antes de agendar')" />
                                </div>

                                <div class="flex items-center space-x-3 pt-6">
                                    <input type="checkbox" wire:model.live="asesorias_sync_to_agenda" id="asesorias_sync_to_agenda" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <x-input-label for="asesorias_sync_to_agenda" :value="__('Sincronizar asesorías a Agenda')" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            @if ($errors->any())
                                <span class="text-sm text-red-600 font-medium">Hay errores en el formulario. Por favor revisa los campos.</span>
                            @endif
                            <x-primary-button>
                                {{ __('Guardar Cambios') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
