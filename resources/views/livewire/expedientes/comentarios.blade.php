<div class="space-y-6">
    {{-- Formulario para nuevo comentario --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="flex-1">
                <textarea 
                    wire:model="nuevoComentario" 
                    rows="3" 
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg resize-none"
                    placeholder="Escribe un comentario sobre este expediente..."
                ></textarea>
                @error('nuevoComentario')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="mt-3 flex justify-between items-center">
                    <p class="text-xs text-gray-500">
                        Solo los abogados asignados pueden comentar
                    </p>
                    <button 
                        wire:click="agregarComentario" 
                        wire:loading.attr="disabled"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="agregarComentario">Publicar</span>
                        <span wire:loading wire:target="agregarComentario">Publicando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de comentarios --}}
    <div class="space-y-4">
        @forelse($comentarios as $comentario)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition">
                <div class="flex items-start space-x-3">
                    {{-- Avatar --}}
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($comentario->user->name, 0, 1)) }}
                        </div>
                    </div>

                    {{-- Contenido del comentario --}}
                    <div class="flex-1 min-w-0">
                        <div class="bg-gray-50 rounded-2xl px-4 py-3">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="font-bold text-gray-900 text-sm">{{ $comentario->user->name }}</h4>
                                @if($comentario->user_id === auth()->id() || auth()->user()->can('manage users'))
                                    <button 
                                        wire:click="eliminarComentario({{ $comentario->id }})"
                                        wire:confirm="¿Estás seguro de eliminar este comentario?"
                                        class="text-gray-400 hover:text-red-600 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <p class="text-gray-700 text-sm whitespace-pre-wrap break-words">{{ $comentario->contenido }}</p>
                        </div>
                        
                        {{-- Metadata --}}
                        <div class="mt-2 px-4 flex items-center space-x-4 text-xs text-gray-500">
                            <span class="font-medium">{{ $comentario->created_at->locale('es')->diffForHumans() }}</span>
                            @if($comentario->created_at != $comentario->updated_at)
                                <span class="text-gray-400">• Editado</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-gray-500 font-medium">No hay comentarios aún</p>
                <p class="text-gray-400 text-sm mt-1">Sé el primero en comentar sobre este expediente</p>
            </div>
        @endforelse
    </div>
</div>
