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
    </style>
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
                        <div class="rounded-2xl px-5 py-3.5 text-sm lg:text-[15px] leading-relaxed shadow-sm 
                            {{ $msg['role'] === 'user' 
                                ? 'bg-indigo-600 text-white rounded-br-sm' 
                                : 'bg-white border border-gray-200 text-gray-800 rounded-bl-sm' }}">
                            {!! \Illuminate\Support\Str::markdown($msg['content']) !!}
                        </div>
                    </div>
                </div>
            @endforeach

             <div wire:loading wire:target="sendMessage" class="flex justify-start">
                 <div class="bg-white border border-gray-100 rounded-2xl px-4 py-2 flex items-center gap-2 shadow-sm">
                    <span class="text-xs text-indigo-500 font-medium italic">Diogenes está procesando...</span>
                    <span class="flex gap-1">
                        <span class="w-1 h-1 bg-indigo-400 rounded-full animate-bounce"></span>
                        <span class="w-1 h-1 bg-indigo-400 rounded-full animate-bounce delay-75"></span>
                        <span class="w-1 h-1 bg-indigo-400 rounded-full animate-bounce delay-150"></span>
                    </span>
                 </div>
            </div>
        </div>

        <!-- Input Area (Fixed Bottom) -->
        <div class="px-4 py-4 lg:px-8 bg-white border-t border-gray-100 flex-shrink-0 z-10 w-full shadow-[0_-4px_12px_-2px_rgba(0,0,0,0.05)]">
            <div class="max-w-3xl mx-auto w-full">
                
                <!-- Input Container -->
                <div class="relative flex items-end gap-2 bg-gray-50 border border-gray-300 focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 rounded-2xl p-2 transition-all shadow-sm">
                    
                    <!-- File Attachment Icon -->
                    <button type="button" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-gray-200 rounded-xl transition-colors flex-shrink-0" title="Adjuntar archivo (Próximamente)">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    </button>

                    <!-- Textarea -->
                    <form wire:submit.prevent="sendMessage;" class="flex-1 w-full min-w-0" id="chat-form">
                        <textarea 
                            wire:model="input" 
                            class="w-full bg-transparent border-none focus:ring-0 py-3 px-2 resize-none text-sm text-gray-800 placeholder-gray-400 max-h-32 scrollbar-hide"
                            placeholder="Escribe tu mensaje..."
                            rows="1"
                            style="min-height: 44px"
                            @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                            @keydown.enter.prevent="if(!$event.shiftKey) { $wire.sendMessage(); $el.value=''; $el.style.height='auto'; }"
                        ></textarea>
                    </form>

                    <!-- Send Button -->
                    <button 
                         @click="$wire.sendMessage();"
                         class="p-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-all shadow-md flex-shrink-0 mb-0.5"
                         :disabled="$wire.isLoading || !$wire.input">
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
        const c = document.getElementById('chat-messages');
        if(c) c.scrollTop = c.scrollHeight;
    </script>
    @endscript
</div>
