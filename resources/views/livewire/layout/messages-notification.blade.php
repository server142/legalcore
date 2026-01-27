<div wire:poll.10s>
    <div x-data="{ open: @entangle('showDropdown') }" @click.away="open = false" class="relative">
        <button @click="$wire.toggleDropdown()" class="relative p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md transition duration-150 ease-in-out">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            @if($unreadCount > 0)
                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full animate-pulse border-2 border-white">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </button>

        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute right-0 mt-2 w-80 max-w-[90vw] bg-white rounded-lg shadow-xl border border-gray-200 z-50"
             style="display: none;">
            
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-900">Comentarios Recientes</h3>
            </div>

            <div class="max-h-96 overflow-y-auto">
                @forelse($recentMessages as $comment)
                    <a href="{{ route('expedientes.show', $comment->expediente_id) }}" 
                       wire:navigate
                       wire:click="markAsRead({{ $comment->id }})"
                       class="block p-4 hover:bg-gray-50 border-b border-gray-100 transition">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $comment->user->name }}</p>
                                    <span class="text-[10px] text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs font-medium text-indigo-600 truncate mb-0.5">{{ $comment->expediente->numero }}</p>
                                <p class="text-xs text-gray-600 truncate">{{ Str::limit($comment->contenido, 50) }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="mt-2 text-sm">No hay comentarios recientes</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <audio id="notification-sound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUKjk77RgGwU7k9n0yHkpBSh+zPLaizsKElyx6OyrWBUIQ5zd8sFuJAUuhM/z2Ik2Bhxqv/DlnUwLDlCo5O+zYBoFPJPZ9Mh5KAUofszy2os7ChJcsei" type="audio/wav">
    </audio>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('play-notification-sound', () => {
                const audio = document.getElementById('notification-sound');
                if (audio) {
                    audio.play().catch(e => console.log('Audio play failed:', e));
                }
            });
        });
    </script>
</div>
