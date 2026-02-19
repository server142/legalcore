<div class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
    <!-- Chat Window -->
    <div x-show="$wire.isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         class="mb-4 bg-white w-96 max-w-[calc(100vw-2rem)] rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col h-[500px]"
         style="display: none;">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 flex justify-between items-center text-white shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm">Diogenes AI</h3>
                    <p class="text-xs text-indigo-100 flex items-center">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></span> En línea (Xalapa)
                    </p>
                </div>
            </div>
            <button wire:click="toggleChat" class="text-white/80 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50/50" id="chat-messages">
            @foreach($messages as $msg)
                <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="msg-content-sales max-w-[85%] rounded-2xl p-3 text-sm shadow-sm {{ $msg['role'] === 'user' ? 'bg-indigo-600 text-white rounded-br-sm' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-sm' }}">
                        {!! \Illuminate\Support\Str::markdown($msg['content']) !!}
                    </div>
                </div>
            @endforeach

            @if($isLoading)
                <div class="flex justify-start">
                    <div class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm p-3 shadow-sm">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-100 shrink-0">
            <form wire:submit.prevent="sendMessage" class="flex items-center space-x-2">
                <input type="text" 
                       wire:model="input" 
                       placeholder="Escribe tu duda aquí..." 
                       class="flex-1 bg-gray-50 border-0 rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:bg-white transition"
                       {{ $isLoading ? 'disabled' : '' }}>
                
                <button type="submit" 
                        class="bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $isLoading ? 'disabled' : '' }}>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" transform="rotate(45)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
            <div class="text-center mt-2">
                 <a href="https://wa.me/522281405060" target="_blank" class="text-xs text-gray-400 hover:text-green-600 transition flex items-center justify-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg> 
                    ¿Prefieres WhatsApp?
                </a>
            </div>
        </div>

        <script>
            // Auto-scroll to bottom of chat
            const chatBox = document.getElementById('chat-messages');
            const observer = new MutationObserver(() => {
                chatBox.scrollTop = chatBox.scrollHeight;
            });
            observer.observe(chatBox, { childList: true, subtree: true });
        </script>
    </div>

    <!-- Toggle Button -->
    <button wire:click="toggleChat" 
            class="group relative flex items-center justify-center w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full shadow-lg hover:shadow-2xl hover:scale-105 transition duration-300 z-50">
        
        <!-- Icon when closed -->
        <div x-show="!$wire.isOpen" class="absolute transition transform duration-300" 
             x-transition:enter="rotate-0 opacity-100" x-transition:leave="-rotate-90 opacity-0">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        </div>
        
        <!-- Icon when open -->
        <div x-show="$wire.isOpen" class="absolute transition transform duration-300" style="display: none;"
             x-transition:enter="rotate-0 opacity-100" x-transition:leave="rotate-90 opacity-0">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
        </div>

        <!-- Notification Dot -->
        @if($hasUnread && !$isOpen)
            <span class="absolute top-0 right-0 block h-4 w-4 rounded-full ring-2 ring-white bg-red-500 animate-pulse"></span>
        @endif
        
        <!-- Tooltip hint -->
        <span class="absolute right-full mr-4 bg-white text-gray-800 text-sm font-bold px-3 py-1 rounded-lg shadow-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
            ¿Dudas? Chatea con Diogenes
        </span>
    </button>

    <style>
        /* Estilo para links de WhatsApp (Pills) en el Bot de Ventas */
        .msg-content-sales a[href*="wa.me"], 
        .msg-content-sales a[href*="whatsapp.com"] {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #22c55e;
            color: white !important;
            padding: 0.4rem 0.8rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .msg-content-sales a[href*="wa.me"]:hover {
            background-color: #16a34a;
            transform: translateY(-1px);
        }
        .msg-content-sales a[href*="wa.me"]::before {
            content: '';
            display: block;
            width: 1em;
            height: 1em;
            background-image: url("data:image/svg+xml,%3Csvg fill='white' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
</div>
