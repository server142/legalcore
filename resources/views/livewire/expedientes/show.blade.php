<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $expediente->numero }} - {{ $expediente->titulo }}
    </h2>
</x-slot>

<div class="p-6">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center space-x-4 w-full md:w-auto">
            <a href="{{ route('expedientes.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition text-gray-500 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="min-w-0">
                <h2 class="text-2xl font-bold text-gray-800 truncate">{{ $expediente->numero }}</h2>
                <p class="text-gray-500 text-sm truncate">{{ $expediente->materia }} | {{ $expediente->juzgado }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 w-full md:w-auto">
            @can('manage users')
                <a href="{{ route('expedientes.assignments', $expediente) }}" wire:navigate class="bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-50 flex items-center justify-center flex-1 md:flex-none whitespace-nowrap text-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Gestionar
                </a>
            @endcan
            <a href="{{ route('reportes.expediente', $expediente) }}" target="_blank" class="bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-50 flex items-center justify-center flex-1 md:flex-none transition shadow-sm text-sm">
                <svg class="w-4 h-4 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Imprimir
            </a>
            <button wire:click="edit" class="bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-50 flex-1 md:flex-none text-sm">Editar</button>
            <button wire:click="toggleAddActuacion" class="bg-indigo-600 text-white px-3 py-2 rounded-lg hover:bg-indigo-700 flex-1 md:flex-none whitespace-nowrap text-sm font-bold">
                + Actuación
            </button>
        </div>
    </div>

    @if($showAddActuacion)
        <div class="mb-6">
            <livewire:expedientes.add-actuacion :expediente="$expediente" />
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Detalles del Asunto</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Estado Procesal</p>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $expediente->estado_procesal }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Cliente</p>
                        <p class="text-sm font-medium">{{ $expediente->cliente->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Abogado Responsable</p>
                        <p class="text-sm font-medium">{{ $expediente->abogado->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Juez</p>
                        <p class="text-sm font-medium">{{ $expediente->nombre_juez ?? 'No asignado' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Fecha de Inicio</p>
                        <p class="text-sm font-medium">{{ $expediente->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content (Tabs) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="flex border-b overflow-x-auto scrollbar-hide">
                    <button wire:click="setTab('actuaciones')" class="px-4 md:px-6 py-3 text-sm font-medium whitespace-nowrap {{ $activeTab == 'actuaciones' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                        Actuaciones
                    </button>
                    <button wire:click="setTab('documentos')" class="px-4 md:px-6 py-3 text-sm font-medium whitespace-nowrap {{ $activeTab == 'documentos' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                        Documentos
                    </button>
                    <button wire:click="setTab('agenda')" class="px-4 md:px-6 py-3 text-sm font-medium whitespace-nowrap {{ $activeTab == 'agenda' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                        Agenda
                    </button>
                </div>

                <div class="p-6">
                    @if($activeTab == 'actuaciones')
                        <div class="space-y-4">
                            @forelse($expediente->actuaciones as $actuacion)
                                <div class="border-l-4 border-indigo-500 bg-gray-50 p-4 rounded-r-lg">
                                    <div class="flex justify-between">
                                        <h4 class="font-bold text-gray-800">{{ $actuacion->titulo }}</h4>
                                        <span class="text-xs text-gray-500">{{ $actuacion->fecha?->format('d/m/Y') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $actuacion->descripcion }}</p>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-8">No hay actuaciones registradas.</p>
                            @endforelse
                        </div>
                    @elseif($activeTab == 'documentos')
                        <div class="mb-6">
                            <livewire:expedientes.upload-document :expediente="$expediente" />
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($expediente->documentos as $doc)
                                <div class="border rounded-xl p-4 flex items-start space-x-4 hover:bg-gray-50 transition group relative">
                                    <div class="p-3 rounded-lg {{ $doc->tipo == 'pdf' ? 'bg-red-100 text-red-600' : ($doc->tipo == 'image' ? 'bg-blue-100 text-blue-600' : ($doc->tipo == 'video' ? 'bg-purple-100 text-purple-600' : ($doc->tipo == 'audio' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'))) }}">
                                        @if($doc->tipo == 'pdf')
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"></path><path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"></path></svg>
                                        @elseif($doc->tipo == 'image')
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                                        @elseif($doc->tipo == 'video')
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path></svg>
                                        @elseif($doc->tipo == 'audio')
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.983 5.983 0 01-1.414 4.243 1 1 0 01-1.415-1.415A3.983 3.983 0 0013 10a3.983 3.983 0 00-1.172-2.828a1 1 0 010-1.415z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $doc->nombre }}</p>
                                        <p class="text-[10px] text-gray-500 uppercase mb-2">
                                            {{ $doc->extension }} | {{ $doc->created_at->locale('es')->diffForHumans() }}
                                        </p>
                                        
                                        <div class="flex flex-wrap gap-y-2 gap-x-4">
                                            <button wire:click="openViewer({{ $doc->id }})" class="flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800 transition whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                Ver
                                            </button>
                                            <a href="{{ route('documentos.show', $doc) }}" download class="flex items-center text-xs font-bold text-green-600 hover:text-green-800 transition whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Descargar
                                            </a>
                                            <button wire:click="deleteDocument({{ $doc->id }})" wire:confirm="¿Estás seguro de eliminar este documento?" class="flex items-center text-xs font-bold text-red-600 hover:text-red-800 transition whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Eliminar
                                            </button>
                                        </div>

                                        <div class="mt-2 pt-2 border-t border-gray-100">
                                            <p class="text-[9px] text-gray-400 truncate">
                                                Subido por: <span class="font-medium text-gray-600">{{ $doc->uploader->name ?? 'Sistema' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 text-center text-gray-500 py-12 bg-gray-50 rounded-xl border-2 border-dashed">
                                    No hay documentos cargados.
                                </div>
                            @endforelse
                        </div>
                    @elseif($activeTab == 'agenda')
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-sm font-bold text-gray-400 uppercase">Eventos Programados</h4>
                            <button wire:click="toggleAddEvent" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 transition shadow-sm">
                                + Programar Evento
                            </button>
                        </div>

                        <!-- Modal para Agregar Evento -->
                        @if($showAddEvent)
                            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="toggleAddEvent"></div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="flex justify-between items-center mb-4 border-b pb-3">
                                                <h3 class="text-lg font-bold text-gray-900">Programar Nuevo Evento</h3>
                                                <button wire:click="toggleAddEvent" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                            <livewire:expedientes.add-event :expediente="$expediente" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            @forelse($expediente->eventos->sortBy('start_time') as $evento)
                                <div class="flex items-start space-x-4 p-4 bg-white border rounded-xl hover:shadow-md transition border-l-4 {{ $evento->tipo == 'audiencia' ? 'border-red-500' : ($evento->tipo == 'cita' ? 'border-blue-500' : 'border-orange-500') }}">
                                    <div class="text-center min-w-[50px] bg-gray-50 p-2 rounded-lg">
                                        <span class="block text-[10px] font-bold text-gray-400 uppercase">{{ $evento->start_time->format('M') }}</span>
                                        <span class="block text-lg font-bold text-gray-800">{{ $evento->start_time->format('d') }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h5 class="font-bold text-gray-900">{{ $evento->titulo }}</h5>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $evento->tipo == 'audiencia' ? 'bg-red-100 text-red-700' : ($evento->tipo == 'cita' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700') }}">
                                                {{ ucfirst($evento->tipo) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $evento->start_time->format('H:i') }} - {{ $evento->end_time->format('H:i') }}
                                        </p>
                                        @if($evento->descripcion)
                                            <p class="text-xs text-gray-600 mt-2 bg-gray-50 p-2 rounded italic">{{ $evento->descripcion }}</p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="text-gray-500">No hay eventos programados para este expediente.</p>
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Visor de Archivos -->
    @if($showViewer && $selectedDoc)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeViewer"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4 border-b pb-3">
                            <h3 class="text-lg font-bold text-gray-900 truncate flex items-center">
                                <span class="mr-2 p-1 bg-indigo-100 text-indigo-600 rounded">
                                    @if($selectedDoc->tipo == 'pdf') PDF @elseif($selectedDoc->tipo == 'image') IMG @else DOC @endif
                                </span>
                                {{ $selectedDoc->nombre }}
                            </h3>
                            <button wire:click="closeViewer" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="bg-gray-100 rounded-xl overflow-hidden flex justify-center items-center min-h-[500px]">
                            @if($selectedDoc->tipo == 'image')
                                <img src="{{ route('documentos.show', $selectedDoc) }}" class="max-w-full max-h-[70vh] object-contain shadow-lg">
                            @elseif($selectedDoc->tipo == 'pdf')
                                <iframe src="{{ route('documentos.show', $selectedDoc) }}" class="w-full h-[70vh]" frameborder="0"></iframe>
                            @elseif($selectedDoc->tipo == 'video')
                                <video controls class="max-w-full max-h-[70vh] shadow-lg">
                                    <source src="{{ route('documentos.show', $selectedDoc) }}" type="video/{{ $selectedDoc->extension }}">
                                    Tu navegador no soporta el reproductor de video.
                                </video>
                            @elseif($selectedDoc->tipo == 'audio')
                                <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md text-center">
                                    <div class="bg-indigo-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-indigo-600">
                                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.983 5.983 0 01-1.414 4.243 1 1 0 01-1.415-1.415A3.983 3.983 0 0013 10a3.983 3.983 0 00-1.172-2.828a1 1 0 010-1.415z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <h4 class="font-bold text-gray-800 mb-4">{{ $selectedDoc->nombre }}</h4>
                                    <audio controls class="w-full">
                                        <source src="{{ route('documentos.show', $selectedDoc) }}" type="audio/{{ $selectedDoc->extension }}">
                                        Tu navegador no soporta el reproductor de audio.
                                    </audio>
                                </div>
                            @else
                                <div class="text-center p-12">
                                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="text-gray-500 font-medium">Este tipo de archivo no se puede previsualizar.</p>
                                    <a href="{{ route('documentos.show', $selectedDoc) }}" download class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                                        Descargar para ver
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Editar Expediente -->
    <x-modal-wire wire:model="showEditModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ __('Editar Expediente') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Número -->
                <div>
                    <x-input-label for="edit_numero" :value="__('Número de Expediente')" />
                    <x-text-input wire:model="numero" id="edit_numero" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                </div>

                <!-- Título -->
                <div>
                    <x-input-label for="edit_titulo" :value="__('Título / Carátula')" />
                    <x-text-input wire:model="titulo" id="edit_titulo" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                </div>

                <!-- Materia -->
                <div>
                    <x-input-label for="edit_materia" :value="__('Materia')" />
                    <x-text-input wire:model="materia" id="edit_materia" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('materia')" class="mt-2" />
                </div>

                <!-- Juzgado -->
                <div>
                    <x-input-label for="edit_juzgado" :value="__('Juzgado')" />
                    <x-text-input wire:model="juzgado" id="edit_juzgado" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('juzgado')" class="mt-2" />
                </div>

                <!-- Estado Procesal -->
                <div>
                    <x-input-label for="edit_estado" :value="__('Estado Procesal')" />
                    <select wire:model="estado_procesal" id="edit_estado" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="Trámite">Trámite</option>
                        <option value="Sentencia">Sentencia</option>
                        <option value="Ejecución">Ejecución</option>
                        <option value="Cerrado">Cerrado</option>
                        <option value="Suspendido">Suspendido</option>
                    </select>
                    <x-input-error :messages="$errors->get('estado_procesal')" class="mt-2" />
                </div>

                <!-- Juez -->
                <div>
                    <x-input-label for="edit_juez" :value="__('Nombre del Juez')" />
                    <x-text-input wire:model="nombre_juez" id="edit_juez" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('nombre_juez')" class="mt-2" />
                </div>

                <!-- Cliente -->
                <div>
                    <x-input-label for="edit_cliente" :value="__('Cliente')" />
                    <select wire:model="cliente_id" id="edit_cliente" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                </div>

                <!-- Abogado -->
                <div>
                    <x-input-label for="edit_abogado" :value="__('Abogado Responsable')" />
                    <select wire:model="abogado_responsable_id" id="edit_abogado" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @foreach($abogados as $abogado)
                            <option value="{{ $abogado->id }}">{{ $abogado->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('abogado_responsable_id')" class="mt-2" />
                </div>

                <!-- Fecha Inicio -->
                <div>
                    <x-input-label for="edit_fecha" :value="__('Fecha de Inicio')" />
                    <x-text-input wire:model="fecha_inicio" id="edit_fecha" class="block mt-1 w-full" type="date" />
                    <x-input-error :messages="$errors->get('fecha_inicio')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('showEditModal', false)">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" wire:click="update">
                    {{ __('Actualizar Expediente') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal-wire>
</div>
