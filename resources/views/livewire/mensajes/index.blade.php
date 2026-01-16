<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mensajes') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" style="height: calc(100vh - 200px);">
                <div class="grid grid-cols-12 h-full">
                    <!-- Sidebar: Conversaciones -->
                    <div class="col-span-4 border-r border-gray-200 flex flex-col">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                            <h3 class="font-bold text-gray-900">Conversaciones</h3>
                            <button wire:click="create" class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto">
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
                    <div class="col-span-8 flex flex-col">
                        @if($selectedConversation)
                            <!-- Header de conversación -->
                            <div class="p-4 border-b border-gray-200 bg-white flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                    {{ substr($selectedConversation->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ $selectedConversation->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $selectedConversation->getRoleNames()->first() }}</p>
                                </div>
                            </div>

                            <!-- Mensajes -->
                            <div class="flex-1 overflow-y-auto p-4 bg-gray-50 space-y-4" id="messages-container">

                                @foreach($messages as $message)
                                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-xs lg:max-w-md">
                                            <div class="rounded-2xl px-4 py-2 {{ $message->sender_id === auth()->id() ? 'bg-indigo-600 text-white' : 'bg-white text-gray-900 shadow-sm' }}">
                                                <p class="text-sm whitespace-pre-wrap break-words">{{ $message->contenido }}</p>
                                                
                                                @if($message->attachment_path)
                                                    <div class="mt-2 pt-2 border-t {{ $message->sender_id === auth()->id() ? 'border-indigo-500' : 'border-gray-100' }}">
                                                        @if(Str::startsWith($message->attachment_type, 'image/'))
                                                            <img src="{{ Storage::url($message->attachment_path) }}" alt="Adjunto" class="max-w-full rounded-lg max-h-48 object-cover">
                                                        @else
                                                            <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="flex items-center space-x-2 text-xs hover:underline">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                                <span>{{ $message->attachment_name }}</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-400 mt-1 {{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                                                {{ $message->created_at->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <!-- Typing Indicator (Placeholder for real-time) -->
                                <div class="hidden" id="typing-indicator">
                                    <div class="flex items-center space-x-1 ml-4">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Input de respuesta -->
                            <div class="p-4 bg-white border-t border-gray-200">
                                <!-- Attachment Preview -->
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
                                            wire:model="replyContent" 
                                            rows="2" 
                                            placeholder="Escribe un mensaje..."
                                            class="w-full border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 resize-none pr-10"
                                            @keydown.enter.prevent="if(!event.shiftKey) { $wire.sendReply(); }"
                                        ></textarea>
                                        
                                        <!-- Emoji Picker Toggle -->
                                        <div class="absolute bottom-2 right-2" x-data="{ showEmojis: false }">
                                            <button type="button" @click="showEmojis = !showEmojis" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </button>
                                            
                                            <div x-show="showEmojis" @click.away="showEmojis = false" class="absolute bottom-10 right-0 bg-white border shadow-lg rounded-lg p-2 grid grid-cols-6 gap-1 w-64 z-50" style="display: none;">
                                                @foreach(['😀','😂','😍','👍','👎','🎉','🔥','❤️','🤔','😢','👋','🙏','🤝','👀','✅','❌','💼','⚖️'] as $emoji)
                                                    <button type="button" @click="$wire.set('replyContent', $wire.replyContent + '{{ $emoji }}'); showEmojis = false;" class="text-xl hover:bg-gray-100 p-1 rounded transition">
                                                        {{ $emoji }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        @error('replyContent') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- File Upload -->
                                    <div x-data>
                                        <input type="file" wire:model="attachment" class="hidden" id="file-upload" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                                        <button type="button" @click="document.getElementById('file-upload').click()" class="p-3 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        </button>
                                    </div>

                                    <button type="submit" class="p-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    </button>
                                </form>
                                <p class="text-xs text-gray-500 mt-2">Presiona Enter para enviar, Shift+Enter para nueva línea</p>
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
            <div class="mt-6 space-y-6">
                <div>
                    <x-input-label for="receiver_id" :value="__('Destinatario')" />
                    <select wire:model="receiver_id" id="receiver_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">-- Seleccionar --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->getRoleNames()->first() }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('receiver_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="contenido" :value="__('Mensaje')" />
                    <textarea wire:model="contenido" id="contenido" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    <x-input-error :messages="$errors->get('contenido')" class="mt-2" />
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('showModal', false)">{{ __('Cancelar') }}</x-secondary-button>
                <x-primary-button class="ml-3" wire:click="send">{{ __('Enviar') }}</x-primary-button>
            </div>
        </div>
    </x-modal-wire>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('message-sent', () => {
                setTimeout(() => {
                    const container = document.getElementById('messages-container');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }, 100);
            });
        });
    </script>
</div>
