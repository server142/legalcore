<div 
    x-data="{ open: false }"
    x-init="$watch('open', value => { 
        if (value) { document.body.style.overflow = 'hidden'; } 
        else { document.body.style.overflow = ''; } 
    })"
    @toggle-ai-assistant.window="open = !open"
    @keydown.escape.window="open = false"
    class="relative z-50"
    x-cloak>

    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="ease-in-out duration-500" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in-out duration-500" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
         @click="open = false"></div>

    <!-- Slide-over Panel -->
    <div class="fixed inset-0 overflow-hidden" x-show="open" style="display: none;">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Mobile: Full Screen (inset-0). Desktop: Slide Over (right-0, pl-10) -->
            <div class="fixed inset-0 sm:inset-y-0 sm:right-0 flex max-w-full sm:pl-10 justify-end">
                <div x-show="open"
                     x-data="{ isMaximized: false }"
                     x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     class="w-full h-full sm:w-screen transition-all duration-300 ease-in-out"
                     :class="isMaximized ? 'sm:max-w-5xl' : 'sm:max-w-md'">
                    
                    <div class="flex h-full flex-col bg-white shadow-xl relative" style="height: 100dvh;">
                        <!-- Header -->
                        <div class="px-5 py-3 sm:py-4 sticky top-0 z-10 shadow-sm border-b border-gray-200" 
                             style="background: #ffffff;">
                            
                            <!-- Top Bar -->
                            <div class="flex items-center justify-between mb-2 sm:mb-3">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-800 tracking-tight leading-tight">Diogenes Intelligence</h2>
                                    <p class="text-[11px] text-slate-500 font-medium hidden sm:block">Asistente Jurídico Contextual v1.0</p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <!-- Maximize Button (Desktop only) -->
                                    <button type="button" @click="isMaximized = !isMaximized" 
                                            class="hidden sm:block p-1.5 rounded text-slate-400 hover:text-indigo-600 hover:bg-slate-100 transition-colors" 
                                            :title="isMaximized ? 'Restaurar tamaño' : 'Maximizar panel'">
                                        <!-- Icon for Maximize -->
                                        <svg x-show="!isMaximized" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                                        </svg>
                                        <!-- Icon for Minimize -->
                                        <svg x-show="isMaximized" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5M15 15l5.25 5.25" />
                                        </svg>
                                    </button>

                                    <button type="button" wire:click="exportChatHistory" 
                                            class="p-1.5 rounded text-slate-400 hover:text-indigo-600 hover:bg-slate-100 transition-colors" 
                                            title="Descargar Historial">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 9.75V1.5m0 0 3 3m-3-3-3 3" />
                                        </svg>
                                    </button>

                                    <button type="button" wire:click="resetChat" 
                                            class="p-1.5 rounded text-slate-400 hover:text-indigo-600 hover:bg-slate-100 transition-colors" 
                                            title="Reiniciar Chat">
                                        <svg class="h-4 w-4 shink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                    </button>

                                    <button type="button" @click="open = false"
                                            class="p-1.5 rounded text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                        <span class="sr-only">Cerrar</span>
                                        <svg class="h-6 w-6 sm:h-5 sm:w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Compact Mode Selector -->
                            <div class="flex gap-1.5 overflow-x-auto pb-1 scrollbar-hide">
                                @foreach(['analyst' => 'Analista', 'drafter' => 'Redactor', 'strategist' => 'Estratega', 'researcher' => 'Investigador'] as $key => $label)
                                    <button wire:click="setMode('{{ $key }}')" 
                                            class="px-3 py-1.5 rounded-full text-[11px] font-semibold whitespace-nowrap transition-all border shrink-0"
                                            style="{{ $mode === $key 
                                                ? 'background-color: #e0e7ff; color: #312e81; border-color: #c7d2fe;' 
                                                : 'background-color: #ffffff; color: #64748b; border-color: #e2e8f0;' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Document Selector -->
                        @if(count($documents) > 0)
                            <div class="px-5 py-2 bg-slate-50 border-b border-gray-200">
                                <label for="doc-select" class="block text-xs font-medium text-gray-500 mb-1">Analizar Documento (Contexto Extra):</label>
                                <select id="doc-select" wire:model.change="selectedDocumentId" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-xs sm:leading-6">
                                    <option value="">-- Ninguno (Solo Expediente) --</option>
                                    @foreach($documents as $doc)
                                        <option value="{{ $doc->id }}" class="text-gray-900">
                                            📄 {{ \Illuminate\Support\Str::limit($doc->nombre, 35) }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($selectedDocumentId)
                                    <p class="mt-2 p-2 bg-indigo-50 rounded border border-indigo-100 text-[10px] text-indigo-700 flex items-start gap-1">
                                        <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Documento cargado. <strong>Escribe tu pregunta en el chat</strong> para analizarlo.</span>
                                    </p>
                                @endif
                            </div>
                        @else
                            <div class="px-5 py-2 bg-slate-50 border-b border-gray-200">
                                <p class="text-[10px] text-gray-400 italic text-center">
                                    No hay documentos PDF/TXT en este expediente para analizar. Sube uno en la pestaña "Documentos".
                                </p>
                            </div>
                        @endif

                        <!-- Chat Body -->
                        <div x-ref="chatContainer" 
                             class="flex-1 overflow-y-auto p-4 bg-gray-50 flex flex-col space-y-4 custom-scrollbar"
                             x-init="$watch('$wire.messages', () => { 
                                setTimeout(() => { $refs.chatContainer.scrollTop = $refs.chatContainer.scrollHeight }, 100); 
                             })">
                            @foreach($messages as $msg)
                                <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} group mb-4">
                                    @if($msg['role'] !== 'user')
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center mr-2 mt-1">
                                            @if($msg['role'] === 'system')
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            @else
                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="flex flex-col max-w-[85%]">
                                        <div class="rounded-lg px-4 py-2 text-sm shadow-sm relative text-message-content
                                            {{ $msg['role'] === 'user' ? 'bg-indigo-600 text-white' : ($msg['role'] === 'system' ? 'bg-gray-200 text-gray-600 text-xs italic' : 'bg-white text-gray-800') }}">
                                            {!! \Illuminate\Support\Str::markdown($msg['content']) !!}
                                        </div>

                                        @if($msg['role'] === 'assistant')
                                            <!-- Metadata Footer -->
                                            <div class="flex justify-between items-center mt-1 px-1">
                                                <!-- Execution Time -->
                                                <div class="text-[10px] text-gray-300 font-mono">
                                                    @if(isset($msg['execution_time']))
                                                        ⏱ {{ $msg['execution_time'] }}s
                                                    @endif
                                                </div>

                                                <!-- Action Buttons Container -->
                                                <div class="flex gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity duration-200" 
                                                     x-data="{ copied: false, speaking: false }">
                                                    
                                                    <!-- Copy Text Button -->
                                                    <button type="button" 
                                                            @click="
                                                                navigator.clipboard.writeText($el.closest('.flex-col').querySelector('.text-message-content').innerText)
                                                                .then(() => {
                                                                    copied = true;
                                                                    setTimeout(() => copied = false, 2000);
                                                                });
                                                            "
                                                            title="Copiar texto"
                                                            class="text-[10px] flex items-center bg-gray-50 px-2 py-1 rounded border border-gray-100 shadow-sm transition-all hover:bg-white"
                                                            :class="copied ? 'text-green-600 border-green-200' : 'text-gray-400 hover:text-indigo-600'">
                                                        <svg x-show="!copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m2 4v6m0 0l-2-2m2 2l2-2"></path></svg>
                                                        <svg x-show="copied" style="display:none;" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        <span x-show="copied" style="display:none;" class="ml-1 font-semibold">¡Copiado!</span>
                                                    </button>

                                                    <!-- Read / Speak Button -->
                                                    <button type="button" 
                                                            @click="
                                                                if (speaking) {
                                                                    window.speechSynthesis.cancel();
                                                                    speaking = false;
                                                                } else {
                                                                    let text = $el.closest('.flex-col').querySelector('.text-message-content').innerText;
                                                                    let utterance = new SpeechSynthesisUtterance(text);
                                                                    utterance.lang = 'es-ES';
                                                                    utterance.rate = 1.1;
                                                                    utterance.onend = () => speaking = false;
                                                                    window.speechSynthesis.speak(utterance);
                                                                    speaking = true;
                                                                }
                                                            "
                                                            :title="speaking ? 'Detener lectura' : 'Escuchar respuesta'"
                                                            class="text-[10px] flex items-center bg-gray-50 px-2 py-1 rounded border border-gray-100 shadow-sm transition-all hover:bg-white text-gray-400 hover:text-pink-600"
                                                            :class="speaking ? 'text-pink-600 ring-1 ring-pink-100 animate-pulse' : ''">
                                                        <svg x-show="!speaking" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                                                        <svg x-show="speaking" style="display:none;" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                                                    </button>

                                                    <!-- Export to Word -->
                                                    <button type="button" wire:click="exportToWord(@js($msg['content']))"
                                                            title="Descargar DOCX"
                                                            class="text-[10px] text-gray-400 hover:text-blue-600 flex items-center bg-gray-50 px-2 py-1 rounded border border-gray-100 shadow-sm transition-all hover:bg-white">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    </button>

                                                    <!-- Save to AI Notes -->
                                                    <button type="button" wire:click="saveAsAiNote(@js($msg['content']))"
                                                            wire:confirm="¿Guardar respuesta en Notas de IA?"
                                                            title="Guardar Nota IA"
                                                            class="text-[10px] text-gray-400 hover:text-green-600 flex items-center bg-gray-50 px-2 py-1 rounded border border-gray-100 shadow-sm transition-all hover:bg-white">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    </button>
                                                    
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <!-- Loading Indicator -->
                            <div wire:loading wire:target="sendMessage" class="flex justify-start">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center mr-2">
                                     <svg class="w-5 h-5 text-indigo-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                                </div>
                                <div class="bg-gray-200 rounded-lg px-4 py-2 text-sm text-gray-500 italic animate-pulse">
                                    Analizando...
                                </div>
                            </div>
                        </div>

                        <!-- Footer Input -->
                        <div class="border-t border-gray-200 px-4 py-3 sm:px-6 sm:py-6 bg-white pb-safe"
                             x-data="{ 
                                recording: false,
                                recognition: null,
                                startRecording() {
                                    if (!('webkitSpeechRecognition' in window)) {
                                        alert('Tu navegador no soporta dictado por voz.');
                                        return;
                                    }
                                    this.recognition = new webkitSpeechRecognition();
                                    this.recognition.lang = 'es-ES';
                                    this.recognition.continuous = false;
                                    this.recognition.interimResults = false;
                                    
                                    this.recognition.onstart = () => { this.recording = true; };
                                    this.recognition.onend = () => { this.recording = false; };
                                    this.recognition.onresult = (event) => {
                                        const transcript = event.results[0][0].transcript;
                                        $wire.set('input', transcript); // Set Livewire model directly
                                    };
                                    
                                    this.recognition.start();
                                },
                                stopRecording() {
                                    if(this.recognition) this.recognition.stop();
                                }
                             }">
                            <form wire:submit.prevent="sendMessage" class="relative">
                                <div class="relative rounded-md shadow-sm">
                                    <input type="text" 
                                           wire:model="input" 
                                           class="block w-full rounded-md border-0 py-3 pl-10 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" 
                                           placeholder="Escribe o dicta tu consulta..." 
                                           {{ $isLoading ? 'disabled' : '' }}>
                                           
                                    <!-- Microphone Button -->
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-2">
                                        <button type="button" 
                                                @click="recording ? stopRecording() : startRecording()"
                                                class="p-1.5 rounded-full hover:bg-gray-100 transition-colors"
                                                :class="recording ? 'text-red-600 animate-pulse' : 'text-gray-400 hover:text-gray-600'">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                        </button>
                                    </div>

                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <button type="submit" 
                                                class="p-1 rounded-md text-indigo-600 hover:text-indigo-500 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 transition-colors"
                                                {{ $isLoading ? 'disabled' : '' }}>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <p class="mt-2 text-center text-xs text-gray-400 hidden sm:block">
                                Diogenes AI puede cometer errores. Verifica la información importante.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
