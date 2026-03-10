<div class="fixed bottom-6 right-6 z-50 flex flex-col items-end" style="font-family: 'Figtree', 'Inter', system-ui, sans-serif;">

    {{-- ── Chat Window ──────────────────────────────────────────────── --}}
    <div x-show="$wire.isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         class="mb-4 bg-white w-96 max-w-[calc(100vw-2rem)] rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col"
         style="height: 500px; display: none;">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 flex justify-between items-center text-white shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm shrink-0">
                    @if($mode === 'support')
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    @else
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    @endif
                </div>
                <div>
                    <h3 class="font-bold text-sm">
                        @if($mode === 'support') Soporte Diogenes AI
                        @else Diogenes AI
                        @endif
                    </h3>
                    <p class="text-xs text-indigo-100 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span>
                        @if($mode === 'support') Asistente de soporte
                        @else En línea · Xalapa
                        @endif
                    </p>
                </div>
            </div>
            <button wire:click="toggleChat" class="text-white/70 hover:text-white transition p-1 rounded-lg hover:bg-white/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/50" id="chat-messages-diogenes">
            @foreach($messages as $msg)
                <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    @if($msg['role'] === 'assistant')
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold mr-2 shrink-0 mt-auto">D</div>
                    @endif
                    <div class="msg-content-bot max-w-[82%] rounded-2xl px-3 py-2.5 text-sm shadow-sm leading-relaxed
                        {{ $msg['role'] === 'user'
                            ? 'bg-indigo-600 text-white rounded-br-sm'
                            : 'bg-white text-gray-800 border border-gray-100 rounded-bl-sm' }}">
                        {!! \Illuminate\Support\Str::markdown($msg['content']) !!}
                    </div>
                </div>
            @endforeach

            @if($isLoading)
                <div class="flex justify-start">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold mr-2 shrink-0">D</div>
                    <div class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                        <div class="flex gap-1">
                            <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:.1s"></div>
                            <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay:.2s"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Input --}}
        <div class="p-3 bg-white border-t border-gray-100 shrink-0">
            <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                <input type="text"
                       wire:model="input"
                       placeholder="{{ $mode === 'support' ? '¿En qué te ayudo?' : '¿Tienes alguna duda?' }}"
                       class="flex-1 bg-gray-50 border-0 rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:bg-white transition"
                       {{ $isLoading ? 'disabled' : '' }}>
                <button type="submit"
                        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-2.5 rounded-full hover:opacity-90 transition shrink-0 disabled:opacity-40"
                        {{ $isLoading ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="transform:rotate(45deg)"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
            <div class="text-center mt-1.5">
                <a href="https://wa.me/522281405060" target="_blank"
                   class="text-xs text-gray-400 hover:text-green-600 transition flex items-center justify-center gap-1 font-medium">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413zm-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    ¿Prefieres WhatsApp?
                </a>
            </div>
        </div>

        <script>
            (() => {
                const chatBox = document.getElementById('chat-messages-diogenes');
                if (!chatBox) return;
                const obs = new MutationObserver(() => { chatBox.scrollTop = chatBox.scrollHeight; });
                obs.observe(chatBox, { childList: true, subtree: true });
                chatBox.scrollTop = chatBox.scrollHeight;
            })();
        </script>
    </div>

    {{-- ── Toggle Button ─────────────────────────────────────────────── --}}
    <button wire:click="toggleChat"
            class="group relative flex items-center justify-center w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 z-50">

        {{-- Icono cerrado --}}
        <div x-show="!$wire.isOpen" class="absolute transition duration-200">
            @if($mode === 'support')
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            @else
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            @endif
        </div>

        {{-- Icono abierto --}}
        <div x-show="$wire.isOpen" class="absolute transition duration-200" style="display: none;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>

        {{-- Notification dot --}}
        @if($hasUnread && !$isOpen)
            <span class="absolute top-0.5 right-0.5 w-4 h-4 bg-red-500 rounded-full ring-2 ring-white animate-pulse"></span>
        @endif

        {{-- Tooltip --}}
        <span class="absolute right-full mr-4 bg-white text-gray-800 text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
            @if($mode === 'support') ¿Necesitas ayuda?
            @elseif($mode === 'sales') ¿Dudas? Chatea con Diogenes
            @else Asistente del Directorio
            @endif
        </span>
    </button>

    <style>
        .msg-content-bot p { margin: 0 0 .35rem; }
        .msg-content-bot p:last-child { margin-bottom: 0; }
        .msg-content-bot a[href*="wa.me"],
        .msg-content-bot a[href*="whatsapp.com"] {
            display: inline-flex; align-items: center; gap: .4rem;
            background: #22c55e; color: white !important;
            padding: .3rem .7rem; border-radius: 9999px;
            text-decoration: none; font-weight: 600; font-size: .72rem;
            margin: .15rem 0; box-shadow: 0 1px 3px rgba(0,0,0,.15);
            transition: all .2s;
        }
        .msg-content-bot a[href*="wa.me"]:hover { background: #16a34a; transform: translateY(-1px); }
    </style>
</div>
