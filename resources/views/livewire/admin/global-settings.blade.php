<div class="p-6">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configuración Global del Sistema') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Sidebar Navigation -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow p-4 space-y-2">
                    <button @click="$dispatch('set-tab', 'stripe')" class="w-full text-left px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium text-gray-700">Configuración Stripe</button>
                    <button @click="$dispatch('set-tab', 'sms')" class="w-full text-left px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium text-gray-700">Configuración SMS</button>
                    <button @click="$dispatch('set-tab', 'mail')" class="w-full text-left px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium text-gray-700">Servidor de Correo</button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="md:col-span-2" x-data="{ tab: 'stripe' }" @set-tab.window="tab = $event.detail">
                <form wire:submit.prevent="save">
                    <!-- Stripe Settings -->
                    <div x-show="tab === 'stripe'" class="bg-white rounded-lg shadow p-6 space-y-4">
                        <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Pasarela de Pagos (Stripe)</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <x-input-label for="stripe_key" value="Stripe Publishable Key" />
                                <x-text-input wire:model="stripe_key" id="stripe_key" class="mt-1 block w-full" type="text" />
                            </div>
                            <div>
                                <x-input-label for="stripe_secret" value="Stripe Secret Key" />
                                <x-text-input wire:model="stripe_secret" id="stripe_secret" class="mt-1 block w-full" type="password" />
                            </div>
                            <div>
                                <x-input-label for="stripe_webhook_secret" value="Stripe Webhook Secret" />
                                <x-text-input wire:model="stripe_webhook_secret" id="stripe_webhook_secret" class="mt-1 block w-full" type="password" />
                            </div>
                        </div>
                    </div>

                    <!-- SMS Settings -->
                    <div x-show="tab === 'sms'" class="bg-white rounded-lg shadow p-6 space-y-4" style="display: none;">
                        <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Alertas SMS (Twilio)</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <x-input-label for="sms_sid" value="Account SID" />
                                <x-text-input wire:model="sms_sid" id="sms_sid" class="mt-1 block w-full" type="text" />
                            </div>
                            <div>
                                <x-input-label for="sms_token" value="Auth Token" />
                                <x-text-input wire:model="sms_token" id="sms_token" class="mt-1 block w-full" type="password" />
                            </div>
                            <div>
                                <x-input-label for="sms_from" value="From Number" />
                                <x-text-input wire:model="sms_from" id="sms_from" class="mt-1 block w-full" type="text" />
                            </div>
                        </div>
                    </div>

                    <!-- Mail Settings -->
                    <div x-show="tab === 'mail'" class="bg-white rounded-lg shadow p-6 space-y-4" style="display: none;">
                        <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Configuración de Correo (SMTP)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <x-input-label for="mail_host" value="SMTP Host" />
                                <x-text-input wire:model="mail_host" id="mail_host" class="mt-1 block w-full" type="text" />
                            </div>
                            <div>
                                <x-input-label for="mail_port" value="SMTP Port" />
                                <x-text-input wire:model="mail_port" id="mail_port" class="mt-1 block w-full" type="text" />
                            </div>
                            <div>
                                <x-input-label for="mail_encryption" value="Encryption" />
                                <select wire:model="mail_encryption" id="mail_encryption" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="mail_username" value="Username" />
                                <x-text-input wire:model="mail_username" id="mail_username" class="mt-1 block w-full" type="text" />
                            </div>
                            <div>
                                <x-input-label for="mail_password" value="Password" />
                                <x-text-input wire:model="mail_password" id="mail_password" class="mt-1 block w-full" type="password" />
                            </div>
                            <div>
                                <x-input-label for="mail_from_address" value="From Address" />
                                <x-text-input wire:model="mail_from_address" id="mail_from_address" class="mt-1 block w-full" type="email" />
                            </div>
                            <div>
                                <x-input-label for="mail_from_name" value="From Name" />
                                <x-text-input wire:model="mail_from_name" id="mail_from_name" class="mt-1 block w-full" type="text" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button type="submit">
                            {{ __('Guardar Configuraciones') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
