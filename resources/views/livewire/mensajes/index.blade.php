<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mensajes') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="height: calc(100vh - 180px);">
            <div class="bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden h-full flex flex-row" wire:poll.15s x-data="{ 
                selectedId: @entangle('selectedConversationId'),
                get showChat() { return this.selectedId !== null }
            }">
                <!-- Sidebar: Conversaciones -->
                <div class="w-full md:w-1/3 lg:w-1/4 border-r border-gray-200 flex flex-col bg-white flex-shrink-0" 
                     x-show="!showChat || window.innerWidth >= 768"
                     :class="{'hidden md:flex': showChat, 'flex': !showChat}">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 flex-shrink-0">
                            <h3 class="font-bold text-gray-900">Conversaciones</h3>
                            <button wire:click="create" class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>

                        <div class="flex-1 min-h-0 overflow-y-auto">
                            @forelse($conversations as $conversation)
                                <div wire:click="selectConversation({{ $conversation->id }})" 
                                     class="p-4 border-b border-gray-100 cursor-pointer transition {{ $selectedConversation && $selectedConversation->id === $conversation->id ? 'bg-indigo-50' : 'hover:bg-gray-50' }}">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                                {{ substr($conversation->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start">
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $conversation->name }}</p>
                                                @if($conversation->last_message)
                                                    <span class="text-xs text-gray-400">{{ $conversation->last_message->created_at->diffForHumans(null, true) }}</span>
                                                @endif
                                            </div>
                                            @if($conversation->last_message)
                                                <p class="text-xs text-gray-600 truncate mt-1">
                                                    {{ $conversation->last_message->sender_id === auth()->id() ? 'Tú: ' : '' }}
                                                    {{ Str::limit($conversation->last_message->contenido, 40) }}
                                                </p>
                                            @endif
                                            @if($conversation->unread_count > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-600 text-white mt-1">
                                                    {{ $conversation->unread_count }} nuevo{{ $conversation->unread_count > 1 ? 's' : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <p class="mt-2 text-sm">No hay conversaciones</p>
                                    <button wire:click="create" class="mt-4 text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                                        Iniciar conversación
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>

                <!-- Panel de Conversación -->
                <div class="flex-1 flex flex-col min-h-0 bg-gray-50" 
                     x-show="showChat || window.innerWidth >= 768"
                     :class="{'flex': showChat, 'hidden md:flex': !showChat}">
                        @if($selectedConversation)
                            <!-- Header de conversación -->
                            <div class="p-4 border-b border-gray-200 bg-white flex items-center justify-between shadow-sm z-10 flex-shrink-0">
                                <div class="flex items-center space-x-3">
                                    <button @click="selectedId = null" class="md:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 rounded-full transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                        {{ substr($selectedConversation->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">{{ $selectedConversation->name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $selectedConversation->getRoleNames()->first() }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Mensajes -->
                            <div class="flex-1 min-h-0 overflow-y-auto p-4 space-y-4 scroll-smooth" id="messages-container">
                                @php $lastDate = null; @endphp
                                @foreach($messages as $message)
                                    @php 
                                        $messageDate = $message->created_at->format('Y-m-d');
                                        $showDate = $lastDate !== $messageDate;
                                        $lastDate = $messageDate;
                                    @endphp

                                    @if($showDate)
                                        <div class="flex justify-center my-4">
                                            <span class="px-3 py-1 bg-gray-200 text-gray-600 text-xs rounded-full font-medium uppercase tracking-wider">
                                                {{ $message->created_at->isToday() ? 'Hoy' : ($message->created_at->isYesterday() ? 'Ayer' : $message->created_at->format('d M Y')) }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }} group">
                                        <div class="max-w-[85%] md:max-w-[70%] lg:max-w-[60%]">
                                            <div class="relative rounded-2xl px-4 py-2.5 shadow-sm {{ $message->sender_id === auth()->id() ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white text-gray-900 rounded-tl-none border border-gray-100' }}">
                                                <p class="text-[15px] leading-relaxed whitespace-pre-wrap break-words">{{ $message->contenido }}</p>
                                                
                                                @if($message->attachment_path)
                                                    <div class="mt-2 pt-2 border-t {{ $message->sender_id === auth()->id() ? 'border-indigo-500' : 'border-gray-100' }}">
                                                        @if(Str::startsWith($message->attachment_type, 'image/'))
                                                            <a href="{{ Storage::url($message->attachment_path) }}" target="_blank">
                                                                <img src="{{ Storage::url($message->attachment_path) }}" alt="Adjunto" class="max-w-full rounded-lg max-h-64 object-cover hover:opacity-90 transition">
                                                            </a>
                                                        @else
                                                            <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="flex items-center space-x-2 p-2 rounded-lg {{ $message->sender_id === auth()->id() ? 'bg-indigo-700 hover:bg-indigo-800' : 'bg-gray-50 hover:bg-gray-100' }} transition">
                                                                <div class="p-2 bg-white/20 rounded">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-xs font-medium truncate">{{ $message->attachment_name }}</p>
                                                                    <p class="text-[10px] opacity-70 uppercase">{{ explode('/', $message->attachment_type)[1] ?? 'FILE' }}</p>
                                                                </div>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex items-center mt-1 space-x-1 {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                                <p class="text-[10px] text-gray-400 font-medium">
                                                    {{ $message->created_at->format('H:i') }}
                                                </p>
                                                @if($message->sender_id === auth()->id())
                                                    <svg class="w-3 h-3 {{ $message->leido ? 'text-blue-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M22.31 6.31L10.5 18.12l-6.81-6.81L2.27 12.73l8.23 8.23 13.23-13.23-1.42-1.42z"/>
                                                        @if($message->leido)
                                                            <path d="M16.5 6.31L10.5 12.31l-1.42-1.42 6-6 1.42 1.42z" />
                                                        @endif
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Input de respuesta -->
                            <div class="p-4 bg-white border-t border-gray-200 flex-shrink-0">
                                @if($attachment)
                                    <div class="mb-2 p-2 bg-gray-50 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            <span class="text-sm text-gray-600 truncate max-w-xs">{{ $attachment->getClientOriginalName() }}</span>
                                        </div>
                                        <button wire:click="$set('attachment', null)" class="text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @endif

                                <form wire:submit.prevent="sendReply" class="flex items-end space-x-2">
                                    <div class="flex-1 relative">
                                        <textarea 
                                            wire:model.live="replyContent" 
                                            rows="1" 
                                            placeholder="Escribe un mensaje..."
                                            class="w-full border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 resize-none pr-10"
                                            @keydown.enter.prevent="if(!event.shiftKey) { $wire.sendReply(); }"
                                        ></textarea>
                                        <x-input-error :messages="$errors->get('replyContent')" class="mt-1" />
                                        
                                        <div class="absolute bottom-2 right-2" x-data="{ showEmojis: false }">
                                            <button type="button" @click="showEmojis = !showEmojis" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </button>
                                            
                                            <div x-show="showEmojis" @click.away="showEmojis = false" class="absolute bottom-10 right-0 bg-white border shadow-lg rounded-lg p-2 grid grid-cols-6 gap-1 w-64 z-50" style="display: none;">
                                                @foreach(['😀','😂','😍','👍','👎','🎉','🔥','❤️','🤔','😢','👋','🙏','🤝','👀','✅','❌','💼','⚖️'] as $emoji)
                                                    <button type="button" @click="$wire.replyContent = $wire.replyContent + '{{ $emoji }}'; showEmojis = false;" class="text-xl hover:bg-gray-100 p-1 rounded transition">
                                                        {{ $emoji }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div x-data>
                                        <input type="file" wire:model="attachment" class="hidden" id="file-upload" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                                        <button type="button" @click="document.getElementById('file-upload').click()" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        </button>
                                    </div>

                                    <button type="submit" class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex-1 flex items-center justify-center text-gray-500">
                                <div class="text-center">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <p class="mt-4 text-lg font-medium">Selecciona una conversación</p>
                                    <p class="text-sm text-gray-400">Elige un contacto para ver los mensajes</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Mensaje -->
    <x-modal-wire wire:model="showModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">{{ __('Nueva Conversación') }}</h2>
            <form wire:submit.prevent="iniciarConversacion">
                <div class="space-y-6">
                    <div>
                        <x-input-label for="receiver_id" :value="__('Destinatario')" />
                        <select wire:model="receiver_id" id="receiver_id" wire:key="select-receiver" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Seleccionar --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->getRoleNames()->first() }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('receiver_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="contenido" :value="__('Mensaje')" />
                        <textarea wire:model="contenido" id="contenido" wire:key="textarea-contenido" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Escribe tu primer mensaje..."></textarea>
                        <x-input-error :messages="$errors->get('contenido')" class="mt-2" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button x-on:click="show = false">
                        {{ __('Cancelar') }}
                    </x-secondary-button>
                    <x-primary-button type="submit" wire:loading.attr="disabled">
                        {{ __('Enviar Mensaje') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal-wire>

    <script>
        document.addEventListener('livewire:init', () => {
            const scrollToBottom = () => {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            };

            // Scroll on message sent or conversation selected
            Livewire.on('message-sent', () => {
                setTimeout(scrollToBottom, 50);
            });

            // Initial scroll
            setTimeout(scrollToBottom, 500);
        });
    </script>
</div>
