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
