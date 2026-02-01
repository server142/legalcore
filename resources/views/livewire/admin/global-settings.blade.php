<div class="p-6">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configuración Global del Sistema') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Sidebar Navigation -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow p-4 space-y-2">
                    <button @click="$dispatch('set-tab', 'stripe')" class="w-full text-left px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium text-gray-700">Configuración Stripe</button>
                    <button @click="$dispatch('set-tab', 'sms')" class="w-full text-left px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium text-gray-700">Configuración SMS</button>
                    <button @click="$dispatch('set-tab', 'mail')" class="w-full text-left px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium text-gray-700">Servidor de Correo</button>
                    <button @click="$dispatch('set-tab', 'ai')" class="w-full text-left px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium text-gray-700">Inteligencia Artificial</button>
                    <button @click="$dispatch('set-tab', 'onboarding')" class="w-full text-left px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium text-gray-700">Onboarding (Bienvenida)</button>
                    <button @click="$dispatch('set-tab', 'general')" class="w-full text-left px-4 py-2 rounded-lg hover:bg-indigo-50 transition font-medium text-gray-700">Configuración General</button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="md:col-span-2" x-data="{ tab: 'stripe' }" @set-tab.window="tab = $event.detail">
                <form wire:submit.prevent="save">
                    <!-- Stripe Settings -->
                    <div x-show="tab === 'stripe'" class="bg-white rounded-lg shadow p-6 space-y-4">
                        <div class="flex justify-between items-center border-b pb-2">
                            <h3 class="text-lg font-bold text-gray-800">Pasarela de Pagos (Stripe)</h3>
                            <button type="button" wire:click="testStripe" wire:loading.attr="disabled" class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-bold hover:bg-indigo-200 transition">
                                <span wire:loading.remove wire:target="testStripe">Probar Conexión</span>
                                <span wire:loading wire:target="testStripe">Probando...</span>
                            </button>
                        </div>
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
                        <div class="flex justify-between items-center border-b pb-2">
                            <h3 class="text-lg font-bold text-gray-800">Alertas SMS (Twilio)</h3>
                            <button type="button" wire:click="testSMS" wire:loading.attr="disabled" class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-bold hover:bg-indigo-200 transition">
                                <span wire:loading.remove wire:target="testSMS">Probar Conexión</span>
                                <span wire:loading wire:target="testSMS">Probando...</span>
                            </button>
                        </div>
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
                        <div class="flex justify-between items-center border-b pb-2">
                            <h3 class="text-lg font-bold text-gray-800">Configuración de Correo (SMTP)</h3>
                            <button type="button" 
                                    @click="let email = prompt('Ingrese el correo electrónico para la prueba:', '{{ auth()->user()->email }}'); if(email) $wire.testMail(email)" 
                                    wire:loading.attr="disabled" 
                                    class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-bold hover:bg-indigo-200 transition">
                                <span wire:loading.remove wire:target="testMail">Enviar Correo de Prueba</span>
                                <span wire:loading wire:target="testMail">Enviando...</span>
                            </button>
                        </div>
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

                    <!-- AI Settings -->
                    <div x-show="tab === 'ai'" class="bg-white rounded-lg shadow p-6 space-y-4" style="display: none;">
                        <div class="flex justify-between items-center border-b pb-2">
                            <h3 class="text-lg font-bold text-gray-800">Inteligencia Artificial (Diogenes AI)</h3>
                            <button type="button" wire:click="testAI" wire:loading.attr="disabled" class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-bold hover:bg-indigo-200 transition">
                                <span wire:loading.remove wire:target="testAI">Probar Conexión OpenAI</span>
                                <span wire:loading wire:target="testAI">Conectando...</span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <x-input-label for="ai_provider" value="Proveedor de IA" />
                                <select wire:model="ai_provider" id="ai_provider" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="openai">OpenAI (Recomendado)</option>
                <option value="groq">Groq (Gratis/Rápido)</option>
                <option value="anthropic">Anthropic (Claude)</option>
                <option value="deepseek">DeepSeek (Económico)</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="ai_model" value="Modelo por Defecto" />
                                <x-text-input wire:model="ai_model" id="ai_model" class="mt-1 block w-full" type="text" placeholder="ej. gpt-4o-mini" />
                                <p class="text-xs text-gray-500 mt-1">Recomendado: <code>gpt-4o-mini</code> (Bajo Costo) o <code>gpt-4o</code> (Alta Precisión).</p>
                            </div>
                            <div>
                                <x-input-label for="ai_api_key" value="API Key" />
                                <x-text-input wire:model="ai_api_key" id="ai_api_key" class="mt-1 block w-full" type="password" />
                            </div>
                        </div>
                    </div>

                    <!-- Onboarding Settings -->
                    <div x-show="tab === 'onboarding'" class="bg-white rounded-lg shadow p-6 space-y-4" style="display: none;">
                        <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Configuración de Bienvenida (Onboarding)</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <x-input-label for="welcome_title" value="Título de Bienvenida" />
                                <x-text-input wire:model="welcome_title" id="welcome_title" class="mt-1 block w-full" type="text" placeholder="ej. Bienvenido a LegalCore" />
                            </div>
                            <div>
                                <x-input-label for="welcome_video_url" value="URL del Video de Bienvenida" />
                                <x-text-input wire:model="welcome_video_url" id="welcome_video_url" class="mt-1 block w-full" type="text" placeholder="https://www.youtube.com/watch?v=..." />
                                <p class="text-xs text-gray-500 mt-1">Soporta enlaces de YouTube o archivos directos (.mp4).</p>
                            </div>
                            <div>
                                <x-input-label for="welcome_message" value="Mensaje de Bienvenida" />
                                <textarea wire:model="welcome_message" id="welcome_message" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- General Settings -->
                    <div x-show="tab === 'general'" class="bg-white rounded-lg shadow p-6 space-y-4" style="display: none;">
                        <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Configuración General</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <x-input-label for="max_file_size_mb" value="Tamaño Máximo de Archivo (MB)" />
                                <x-text-input wire:model="max_file_size_mb" id="max_file_size_mb" class="mt-1 block w-full" type="number" min="1" />
                                <p class="text-xs text-gray-500 mt-1">Define el tamaño máximo permitido para cada archivo subido al sistema.</p>
                                <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                    <p class="text-[10px] text-amber-700 font-medium">
                                        <strong>Nota importante:</strong> Para que este cambio sea efectivo con archivos muy grandes (ej. > 100MB), también debes asegurarte de que tu servidor (PHP y Nginx) permita esos tamaños en sus configuraciones (`upload_max_filesize`, `post_max_size` y `client_max_body_size`).
                                    </p>
                                </div>
                            </div>
                            
                            <div class="block mt-4">
                                <x-input-label for="ocr_mode" value="Estrategia de Lectura de Documentos (OCR)" />
                                <select wire:model="ocr_mode" id="ocr_mode" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="off">DESACTIVADO - Solo leer texto nativo (Ahorra RAM)</option>
                                    <option value="local">LOCAL (Tesseract) - Gratis, Procesamiento en Servidor (Requiere RAM)</option>
                                    <option value="vision">NUBE (OpenAI Vision) - Pago por Uso, Cero carga al Servidor</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span x-show="$wire.ocr_mode === 'off'">El sistema solo leerá PDFs que ya tengan texto digital seleccionable. Las imágenes o escaneos serán ignorados.</span>
                                    <span x-show="$wire.ocr_mode === 'local'">Usa el motor Tesseract instalado en este servidor. Puede causar lentitud o errores 502 si el servidor tiene poca RAM (< 2GB).</span>
                                    <span x-show="$wire.ocr_mode === 'vision'">Envía las imágenes de los documentos a OpenAI para ser leídas. Es la opción más robusta y precisa, pero genera costos extra de API (~$0.01 por 50 páginas).</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Guardar Configuraciones</span>
                            <span wire:loading>Guardando...</span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
