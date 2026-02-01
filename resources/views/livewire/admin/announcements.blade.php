<div class="py-6 transition-all duration-300">
    <x-slot name="header">
        <x-header title="Anuncios del Sistema" />
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if (session()->has('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-md shadow-sm animate-pulse">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Formulario de Redacción -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 transition-all hover:shadow-2xl">
                <div class="p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Redactar Nuevo Anuncio
                    </h3>

                    <form wire:submit.prevent="send" class="space-y-6">
                        <div>
                            <x-input-label for="target" value="Destinatarios" />
                            <select wire:model="target" id="target" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                <option value="all">Todos los Usuarios Activos</option>
                                <option value="admins">Solo Administradores de Despacho</option>
                                <option value="superadmins">Solo Súper Administradores</option>
                            </select>
                            <x-input-error for="target" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="subject" value="Asunto del Correo" />
                            <x-text-input wire:model="subject" id="subject" type="text" class="mt-1 block w-full rounded-xl" placeholder="Ej: Nueva funcionalidad: Gestión de Pagos disponible" />
                            <x-input-error for="subject" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="content" value="Cuerpo del Mensaje" />
                            <textarea wire:model="content" id="content" rows="10" 
                                class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all font-sans" 
                                placeholder="Escribe aquí el contenido del anuncio..."></textarea>
                            <x-input-error for="content" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-2 italic">Tip: El mensaje se enviará con un formato elegante y profesional.</p>
                        </div>

                        <div class="flex justify-between items-center pt-4">
                            <button type="button" 
                                    wire:click="sendTest"
                                    wire:loading.attr="disabled"
                                    class="text-indigo-600 font-bold text-sm hover:underline flex items-center gap-1 group">
                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Enviar prueba a mi correo
                            </button>

                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="group relative inline-flex items-center justify-center px-8 py-3 font-semibold text-white transition-all bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                                <span wire:loading.remove wire:target="send">
                                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    Publicar Anuncio Global
                                </span>
                                <span wire:loading wire:target="send">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Enviando a todos...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Previsualización -->
            <div class="space-y-6">
                <div class="bg-gray-100 rounded-2xl p-6 border-2 border-dashed border-gray-300">
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4">Previsualización en Tiempo Real</h4>
                    
                    <div class="bg-white rounded-xl shadow-inner border border-gray-200 overflow-hidden min-h-[500px] flex flex-col">
                        <!-- Email Header Browser Mockup -->
                        <div class="bg-gray-50 border-b border-gray-200 p-3 flex items-center gap-2">
                            <div class="flex gap-1">
                                <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
                            </div>
                            <div class="flex-grow text-center text-[10px] text-gray-400 truncate px-4 bg-white rounded border border-gray-200 py-0.5">
                                {{ $subject ?: 'Sin Asunto' }}
                            </div>
                        </div>

                        <!-- Email Content Body -->
                        <div class="p-8 flex-grow">
                             <div class="max-w-md mx-auto">
                                <div class="flex justify-center mb-8">
                                    <div class="h-10 w-32 bg-gray-200 rounded animate-pulse flex items-center justify-center text-[10px] text-gray-400 font-bold uppercase">Logo Despacho</div>
                                </div>
                                <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $subject ?: 'Asunto del Anuncio' }}</h1>
                                <div class="text-gray-600 leading-relaxed whitespace-pre-wrap text-sm min-h-[200px]">
                                    {{ $content ?: 'Aquí se mostrará la previsualización de tu mensaje a medida que escribas...' }}
                                </div>
                                <div class="mt-8 pt-8 border-t border-gray-100 text-xs text-gray-400">
                                    Gracias,<br>
                                    <b>{{ config('app.name') }}</b>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Ayuda/Tips -->
                <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
                    <h5 class="font-bold text-indigo-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        Consejos para tus anuncios
                    </h5>
                    <ul class="text-sm text-indigo-800 space-y-2 list-disc list-inside opacity-80">
                        <li>Sé conciso y directo en el asunto.</li>
                        <li>Usa párrafos cortos para facilitar la lectura.</li>
                        <li>Los envíos se procesan en segundo plano (cola) para no saturar el servidor.</li>
                        <li>Recuerda verificar tu configuración SMTP antes de enviar masivamente.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
