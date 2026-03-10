<x-slot name="header">
    <x-header title="{{ __('Asistente Jurídico IA') }}" subtitle="Inteligencia artificial aplicada a su despacho" />
</x-slot>

<div 
    id="global-ai-container"
    x-data="{ 
        mobileSidebarOpen: false, 
        isMaximized: false 
    }"
    class="bg-white border-x border-b border-gray-200 overflow-hidden transition-all duration-300 ease-in-out flex shadow-sm"
    :class="isMaximized 
        ? 'fixed inset-0 z-50 rounded-none h-screen w-screen m-0' 
        : 'relative w-full'"
    style="height: calc(100vh - 64px);"
>
    @push('styles')
    <style>
        /* Bloqueamos el scroll del contenedor principal de la app */
        main { 
            overflow: hidden !important; 
            padding: 0 !important; 
            height: calc(100vh - 64px) !important;
        }
        /* Ajuste para que el scroll del main no interfiera */
        body { overflow: hidden; }
        
        /* Personalización para que el área de mensajes ocupe todo el espacio sobrante */
        #chat-messages {
            flex: 1 1 0%;
            overflow-y: auto;
        }

        /* Estilo para links de WhatsApp (Pills) */
        .msg-content a[href*="wa.me"], 
        .msg-content a[href*="whatsapp.com"] {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #22c55e;
            color: white !important;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .msg-content a[href*="wa.me"]:hover {
            background-color: #16a34a;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        }
        .msg-content a[href*="wa.me"]::before {
            content: '';
            display: block;
            width: 1.1em;
            height: 1.1em;
            background-image: url("data:image/svg+xml,%3Csvg fill='white' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
        }
    @endpush
    
    <!-- 1. SIDEBAR HISTORIAL (Diseño Restaurado w-48) -->
    <div class="hidden md:flex md:w-48 md:flex-col bg-gray-50 border-r border-gray-200 flex-shrink-0">
        <!-- Header -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 bg-white">
            <span class="font-bold text-gray-700 text-xs uppercase tracking-wider">Historial</span>
            <button wire:click="newChat" class="text-indigo-600 hover:bg-indigo-50 p-1.5 rounded-md transition-colors" title="Nuevo Chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>

        <!-- Chat List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            @forelse($chats as $chat)
                <button 
                    wire:click="loadChat({{ $chat->id }})" 
                    class="w-full text-left group flex items-start gap-2 p-2.5 rounded-lg text-xs transition-colors border {{ $activeChatId === $chat->id ? 'bg-white text-indigo-700 border-gray-200 shadow-sm font-semibold' : 'border-transparent text-gray-600 hover:bg-gray-100' }}"
                >
                    <span class="truncate flex-1 leading-snug">{{ $chat->title ?? 'Conversación' }}</span>
                    @if($activeChatId === $chat->id)
                        <div class="w-1.5 h-1.5 mt-1 rounded-full bg-indigo-500 flex-shrink-0"></div>
                    @endif
                    <div wire:click.stop="deleteChat({{ $chat->id }})" class="opacity-0 group-hover:opacity-100 hover:text-red-500 transition-opacity">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                </button>
            @empty
                <div class="text-center py-10 text-[10px] text-gray-400">
                    Sin historial
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="p-3 border-t border-gray-200 bg-gray-50 text-[10px] text-gray-400 text-center uppercase tracking-wider">
            &nbsp;
        </div>
    </div>

    <!-- Mobile Sidebar -->
    <div x-show="mobileSidebarOpen" class="absolute inset-0 z-40 bg-black/50 md:hidden backdrop-blur-[1px]" @click="mobileSidebarOpen = false" x-transition.opacity></div>
    <div x-show="mobileSidebarOpen" class="absolute inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transition-transform transform md:hidden flex flex-col"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full">
         <div class="p-4 border-b flex justify-between items-center bg-gray-50">
             <span class="font-bold text-gray-700">Historial</span>
             <button @click="mobileSidebarOpen = false"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
         </div>
         <div class="flex-1 overflow-y-auto p-2">
             @foreach($chats as $chat)
                <button wire:click="loadChat({{ $chat->id }}); mobileSidebarOpen = false" class="w-full text-left p-3 rounded hover:bg-gray-100 text-sm truncate {{ $activeChatId === $chat->id ? 'text-indigo-600 font-bold' : 'text-gray-600' }}">
                    {{ $chat->title ?? 'Chat' }}
                </button>
             @endforeach
         </div>
         <div class="p-4 border-t">
             <button wire:click="newChat" @click="mobileSidebarOpen = false" class="w-full bg-indigo-600 text-white py-2 rounded-lg text-sm">Nuevo Chat</button>
         </div>
    </div>


    <!-- 2. CHAT AREA -->
    <!-- 'overflow-hidden' and 'h-full' are CRITICAL locks to prevent page expansion -->
    <div class="flex-1 flex flex-col min-w-0 bg-white relative overflow-hidden h-full">
        
        <!-- Toolbar -->
        <div class="h-16 border-b border-gray-100 flex items-center justify-between px-4 lg:px-6 flex-shrink-0 bg-white z-10">
            <div class="flex items-center gap-3">
                <button @click="mobileSidebarOpen = true" class="md:hidden text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center text-white shadow-sm shadow-indigo-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800 text-sm">Diogenes AI</h2>
                    <p class="text-[10px] text-gray-500 font-medium">Asistente Jurídico</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                 <a href="{{ $supportUrl }}" target="_blank" class="hidden md:flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-50 hover:bg-green-100 rounded-lg transition-colors border border-green-200" title="Contactar Soporte Humano">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    Soporte
                 </a>
                 <button @click="isMaximized = !isMaximized" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-gray-50 rounded-lg transition-colors" title="Expandir / Contraer">
                    <svg x-show="!isMaximized" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                    <svg x-show="isMaximized" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Messages Scroller -->
        <div id="chat-messages" class="flex-1 overflow-y-auto min-h-0 p-4 lg:p-6 space-y-6 scroll-smooth bg-gray-50/50">
            @if(empty($messages))
                <div class="h-full flex flex-col items-center justify-center select-none pb-20">
                    <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mb-6 shadow-sm border border-gray-100">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                    <p class="text-gray-400 text-sm font-medium">Inicia una conversación con Diogenes</p>
                </div>
            @endif

            @foreach($messages as $msg)
                <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} animate-fade-in-up">
                    <div class="max-w-[85%] lg:max-w-[75%] group">
                        <div class="msg-content rounded-2xl px-5 py-3.5 text-sm lg:text-[15px] leading-relaxed shadow-sm 
                            {{ $msg['role'] === 'user' 
                                ? 'bg-indigo-600 text-white rounded-br-sm' 
                                : 'bg-white border border-gray-200 text-gray-800 rounded-bl-sm' }}">
                            {!! \Illuminate\Support\Str::markdown($msg['content']) !!}
                        </div>
                    </div>
                </div>
            @endforeach

            @if($isLoading)
             <div class="flex justify-start">
                 <div class="bg-white border border-gray-100 rounded-2xl px-4 py-2 flex items-center gap-2 shadow-sm">
                    <span class="text-xs text-indigo-500 font-medium italic">Diogenes está procesando...</span>
                    <span class="flex gap-1">
                        <span class="w-1 h-1 bg-indigo-400 rounded-full animate-bounce"></span>
                        <span class="w-1 h-1 bg-indigo-400 rounded-full animate-bounce delay-75"></span>
                        <span class="w-1 h-1 bg-indigo-400 rounded-full animate-bounce delay-150"></span>
                    </span>
                 </div>
            </div>
            @endif
        </div>

        <!-- Input Area (Fixed Bottom) -->
        <div 
             x-data="{ 
                localInput: '',
                submitMessage() {
                    if (!this.localInput.trim()) return;
                    // Call Livewire method with content
                    $wire.sendMessage(this.localInput);
                    // Clear immediately
                    this.localInput = '';
                    // Reset height
                    this.$refs.chatInput.style.height = '44px';
                }
             }"
             class="px-4 py-4 lg:px-8 bg-white border-t border-gray-100 flex-shrink-0 z-10 w-full shadow-[0_-4px_12px_-2px_rgba(0,0,0,0.05)]"
        >
            <div class="max-w-3xl mx-auto w-full">
                
                <!-- Input Container -->
                <div class="relative flex items-end gap-2 bg-gray-50 border border-gray-300 focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 rounded-2xl p-2 transition-all shadow-sm">
                    
                    <!-- File Attachment Icon -->
                    <button type="button" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-gray-200 rounded-xl transition-colors flex-shrink-0" title="Adjuntar archivo (Próximamente)">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    </button>

                    <!-- Textarea handled by Alpine -->
                    <div class="flex-1 w-full min-w-0" id="chat-form">
                        <textarea 
                            x-ref="chatInput"
                            x-model="localInput"
                            class="w-full bg-transparent border-none focus:ring-0 py-3 px-2 resize-none text-sm text-gray-800 placeholder-gray-400 max-h-32 scrollbar-hide"
                            placeholder="Escribe tu mensaje..."
                            rows="1"
                            style="min-height: 44px"
                            @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                            @keydown.enter.prevent="if(!$event.shiftKey) { submitMessage(); }"
                        ></textarea>
                    </div>

                    <!-- Send Button -->
                    <button 
                         @click="submitMessage()"
                         class="p-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-all shadow-md flex-shrink-0 mb-0.5"
                         :disabled="!localInput.trim() || $wire.isLoading">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>

                </div>
                <div class="text-center mt-2">
                    <p class="text-[10px] text-gray-400">Diogenes AI v1.0 - puede cometer errores. Verifica la información importante.</p>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('chatUpdated', () => {
             const c = document.getElementById('chat-messages');
             if(c) setTimeout(() => c.scrollTo({ top: c.scrollHeight, behavior: 'smooth' }), 50);
        });
        
        // Listener para procesar la IA en un segundo request (sin bloquear UI)
        $wire.on('start-ai-processing', () => {
             $wire.processAIResponse();
        });

        const c = document.getElementById('chat-messages');
        if(c) c.scrollTop = c.scrollHeight;
    </script>
    @endscript
</div>
