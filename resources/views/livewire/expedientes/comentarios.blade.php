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
                        @if($editando === $comentario->id)
                            {{-- Modo edición --}}
                            <div class="space-y-2">
                                <textarea 
                                    wire:model="contenidoEditado" 
                                    rows="3" 
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg resize-none"
                                ></textarea>
                                <div class="flex justify-end space-x-2">
                                    <button wire:click="cancelarEdicion" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">
                                        Cancelar
                                    </button>
                                    <button wire:click="guardarEdicion" class="px-3 py-1 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        Guardar
                                    </button>
                                </div>
                            </div>
                        @else
                            {{-- Modo visualización --}}
                            <div class="bg-gray-50 rounded-2xl px-4 py-3">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-bold text-gray-900 text-sm">{{ $comentario->user->name }}</h4>
                                    @if($comentario->user_id === auth()->id())
                                        <div class="flex items-center space-x-2">
                                            <button 
                                                wire:click="editar({{ $comentario->id }})"
                                                class="text-gray-400 hover:text-indigo-600 transition"
                                                title="Editar"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button 
                                                wire:click="eliminarComentario({{ $comentario->id }})"
                                                wire:confirm="¿Estás seguro de eliminar este comentario?"
                                                class="text-gray-400 hover:text-red-600 transition"
                                                title="Eliminar"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @elseif(auth()->user()->can('manage users'))
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
                            
                            {{-- Metadata y acciones --}}
                            <div class="mt-2 px-4">
                                {{-- Fila 1: Timestamp y Contador --}}
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2 text-xs text-gray-500">
                                        <span class="font-medium">{{ $comentario->created_at->locale('es')->diffForHumans() }}</span>
                                        @if($comentario->created_at != $comentario->updated_at)
                                            <span class="text-gray-400">• Editado</span>
                                        @endif
                                    </div>
                                    
                                    @if($comentario->reacciones->count() > 0)
                                        <span class="text-indigo-600 text-xs font-medium bg-indigo-50 px-2 py-0.5 rounded-full">
                                            {{ $comentario->reacciones->count() }} {{ $comentario->reacciones->count() === 1 ? 'reacción' : 'reacciones' }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Fila 2: Botones de Acción --}}
                                <div class="flex items-center space-x-4 border-t border-gray-100 pt-2">
                                    {{-- Botones de reacción --}}
                                    <div class="flex items-center space-x-1">
                                        @php
                                            $miReaccion = $comentario->reacciones->where('user_id', auth()->id())->first();
                                        @endphp
                                        
                                        <button 
                                            wire:click="toggleReaccion({{ $comentario->id }}, 'like')"
                                            class="p-1.5 rounded-full hover:bg-gray-100 transition {{ $miReaccion && $miReaccion->tipo === 'like' ? 'text-blue-600 bg-blue-50' : 'text-gray-500' }}"
                                            title="Me gusta"
                                        >
                                            <svg class="w-5 h-5" fill="{{ $miReaccion && $miReaccion->tipo === 'like' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                            </svg>
                                        </button>
                                        
                                        <button 
                                            wire:click="toggleReaccion({{ $comentario->id }}, 'love')"
                                            class="p-1.5 rounded-full hover:bg-gray-100 transition {{ $miReaccion && $miReaccion->tipo === 'love' ? 'text-red-600 bg-red-50' : 'text-gray-500' }}"
                                            title="Me encanta"
                                        >
                                            <svg class="w-5 h-5" fill="{{ $miReaccion && $miReaccion->tipo === 'love' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Botón responder --}}
                                    <button 
                                        wire:click="responder({{ $comentario->id }})"
                                        class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition flex items-center"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                        Responder
                                    </button>
                                </div>
                            </div>

                            {{-- Formulario de respuesta inline --}}
                            @if($respondiendo === $comentario->id)
                                <div class="mt-3">
                                    <textarea 
                                        wire:model="replyContent" 
                                        rows="2" 
                                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg resize-none text-sm"
                                        placeholder="Escribe tu respuesta..."
                                    ></textarea>
                                    <div class="flex justify-end space-x-2 mt-2">
                                        <button wire:click="cancelarRespuesta" class="px-3 py-1 text-xs text-gray-600 hover:text-gray-800">
                                            Cancelar
                                        </button>
                                        <button wire:click="publicarRespuesta" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold">
                                            Responder
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Respuestas --}}
                            @if($comentario->respuestas->count() > 0)
                                <div class="mt-4 ml-8 space-y-3 border-l-2 border-gray-200 pl-4">
                                    @foreach($comentario->respuestas as $respuesta)
                                        <div class="flex items-start space-x-2">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold text-xs">
                                                    {{ strtoupper(substr($respuesta->user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="bg-gray-100 rounded-xl px-3 py-2">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <h5 class="font-bold text-gray-900 text-xs">{{ $respuesta->user->name }}</h5>
                                                        @if($respuesta->user_id === auth()->id() || auth()->user()->can('manage users'))
                                                            <button 
                                                                wire:click="eliminarComentario({{ $respuesta->id }})"
                                                                wire:confirm="¿Eliminar respuesta?"
                                                                class="text-gray-400 hover:text-red-600"
                                                            >
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <p class="text-gray-700 text-xs whitespace-pre-wrap break-words">{{ $respuesta->contenido }}</p>
                                                </div>
                                                <div class="mt-1 px-3 flex items-center space-x-3 text-xs text-gray-500">
                                                    <span>{{ $respuesta->created_at->locale('es')->diffForHumans() }}</span>
                                                    @if($respuesta->reacciones->count() > 0)
                                                        <span class="text-indigo-600">{{ $respuesta->reacciones->count() }} 👍</span>
                                                    @endif
                                                    @php
                                                        $miReaccionRespuesta = $respuesta->reacciones->where('user_id', auth()->id())->first();
                                                    @endphp
                                                    <button 
                                                        wire:click="toggleReaccion({{ $respuesta->id }}, 'like')"
                                                        class="font-medium hover:text-indigo-600 flex items-center"
                                                    >
                                                        @if($miReaccionRespuesta && $miReaccionRespuesta->tipo === 'like')
                                                            <svg class="w-4 h-4 text-blue-600 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                                            </svg>
                                                        @else
                                                            Me gusta
                                                        @endif
                                                    </button>
                                                    <button 
                                                        wire:click="responder({{ $respuesta->id }})"
                                                        class="font-medium hover:text-indigo-600"
                                                    >
                                                        Responder
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            {{-- Formulario de respuesta inline para respuesta --}}
                                            @if($respondiendo === $respuesta->id)
                                                <div class="mt-2">
                                                    <textarea 
                                                        wire:model="replyContent" 
                                                        rows="2" 
                                                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg resize-none text-sm"
                                                        placeholder="Responder a {{ $respuesta->user->name }}..."
                                                    ></textarea>
                                                    <div class="flex justify-end space-x-2 mt-2">
                                                        <button wire:click="cancelarRespuesta" class="px-3 py-1 text-xs text-gray-600 hover:text-gray-800">
                                                            Cancelar
                                                        </button>
                                                        <button wire:click="publicarRespuesta" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold">
                                                            Responder
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
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
