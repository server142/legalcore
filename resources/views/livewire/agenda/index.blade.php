<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agenda Judicial') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-end">
                <x-primary-button wire:click="create">
                    {{ __('Nuevo Evento') }}
                </x-primary-button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar: Próximos Eventos (Oculto en móvil para priorizar el calendario) -->
                <div class="hidden lg:block lg:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Próximos 7 días</h3>
                        <div class="space-y-4">
                            @forelse($eventos as $evento)
                                <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded-lg transition group">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-50 flex flex-col items-center justify-center text-indigo-600">
                                        <span class="text-[10px] font-bold uppercase">{{ $evento->start_time->format('M') }}</span>
                                        <span class="text-sm font-bold">{{ $evento->start_time->format('d') }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">
                                            {{ $evento->expediente_id ? "📂 " : "👤 " }}{{ $evento->titulo }}
                                        </p>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            {{ $evento->start_time->format('H:i') }}
                                            @if($evento->google_event_id)
                                                <span class="ml-2 text-green-500" title="Sincronizado con Google Calendar">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.5,19c-3.037,0-5.5-2.463-5.5-5.5c0-3.037,2.463-5.5,5.5-5.5s5.5,2.463,5.5,5.5C23,16.537,20.537,19,17.5,19z M17.5,10 c-1.93,0-3.5,1.57-3.5,3.5s1.57,3.5,3.5,3.5s3.5-1.57,3.5-3.5S19.43,10,17.5,10z M17.5,16c-1.379,0-2.5-1.121-2.5-2.5s1.121-2.5,2.5-2.5 s2.5,1.121,2.5,2.5S18.879,16,17.5,16z"/><path d="M19,13h-3c-0.552,0-1-0.448-1-1s0.448-1,1-1h3c0.552,0,1,0.448,1,1S19.552,13,19,13z"/><path d="M17.5,15c-0.552,0-1-0.448-1-1v-3c0-0.552,0.448-1,1-1s1,0.448,1,1v3C18.5,14.552,18.052,15,17.5,15z"/><path d="M12.03,12.14c-0.01-0.05-0.03-0.09-0.03-0.14c0-2.76,2.24-5,5-5c0.28,0,0.54,0.03,0.8,0.07c0.14-2.32,2.07-4.14,4.45-4.06 c2.25,0.08,4.02,1.91,4.01,4.16c0,0.05,0,0.1,0,0.15c2.16,0.61,3.74,2.59,3.74,4.93c0,2.83-2.3,5.13-5.13,5.13h-0.87 c-0.55,0-1-0.45-1-1s0.45-1,1-1h0.87c1.73,0,3.13-1.4,3.13-3.13c0-1.55-1.13-2.84-2.63-3.08c-0.34-0.05-0.6-0.33-0.62-0.67 c-0.03-1.4-1.17-2.52-2.57-2.49c-1.32,0.03-2.39,1.1-2.42,2.42c0,0.34-0.24,0.63-0.57,0.68c-1.84,0.28-3.11,1.99-2.83,3.83 c0.04,0.26,0.13,0.51,0.25,0.74c0.25,0.49,0.05,1.09-0.44,1.34s-1.09,0.05-1.34-0.44C12.18,12.68,12.09,12.42,12.03,12.14z"/></svg>
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition">
                                        <button wire:click="edit({{ $evento->id }})" class="p-1 text-indigo-600 hover:bg-indigo-50 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $evento->id }})" class="p-1 text-red-600 hover:bg-red-50 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic">No hay eventos próximos.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-indigo-600 p-6 rounded-xl shadow-lg text-white">
                        <h4 class="font-bold mb-2">Tipos de Eventos</h4>
                        <ul class="text-xs space-y-2 opacity-90">
                            <li class="flex items-center"><span class="w-3 h-3 bg-red-400 rounded-full mr-2"></span> Audiencias</li>
                            <li class="flex items-center"><span class="w-3 h-3 bg-blue-400 rounded-full mr-2"></span> Citas / Reuniones</li>
                            <li class="flex items-center"><span class="w-3 h-3 bg-orange-400 rounded-full mr-2"></span> Términos Legales</li>
                        </ul>
                    </div>
                </div>

                <!-- Main: Calendario Interactivo -->
                <div class="lg:col-span-3">
                    <div class="bg-white p-2 md:p-6 rounded-xl shadow-sm border border-gray-100 min-h-[500px] md:min-h-[600px]">
                        <div id="calendar" 
                             x-data="{ events: @js($calendarEvents) }"
                             x-init="
                                 const init = () => {
                                     if (typeof FullCalendar !== 'undefined') {
                                         var calendar = new FullCalendar.Calendar($el, {
                                             initialView: window.innerWidth < 768 ? 'listWeek' : 'dayGridMonth',
                                             locale: 'es',
                                             headerToolbar: {
                                                 left: window.innerWidth < 768 ? 'prev,next' : 'prev,next today',
                                                 center: 'title',
                                                 right: window.innerWidth < 768 ? 'listWeek,dayGridMonth' : 'dayGridMonth,timeGridWeek,timeGridDay'
                                             },
                                             height: 'auto',
                                             events: events,
                                             eventClick: function(info) {
                                                 @this.edit(info.event.id);
                                             },
                                             eventContent: function(arg) {
                                                 let italicEl = document.createElement("div");
                                                 let syncIcon = arg.event.extendedProps.google_event_id 
                                                     ? " <span class='ml-1 text-[10px]'>☁️</span>" 
                                                     : "";
                                                 italicEl.innerHTML = "<div class='fc-content flex items-center overflow-hidden text-xs'>" + 
                                                                    "<span class='truncate'>" + arg.event.title + "</span>" + 
                                                                    syncIcon + 
                                                                    "</div>";
                                                 return { domNodes: [italicEl] };
                                             },
                                             buttonText: {
                                                 today: 'Hoy',
                                                 month: 'Mes',
                                                 week: 'Semana',
                                                 day: 'Día',
                                                 list: 'Lista'
                                             }
                                         });
                                         calendar.render();
                                     } else {
                                         setTimeout(init, 100);
                                     }
                                 };
                                 init();
                             " 
                             wire:ignore></div>
                    </div>
                </div>

                <!-- Sidebar en móvil (al final) -->
                <div class="lg:hidden space-y-6 mt-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Próximos 7 días</h3>
                        <div class="space-y-4">
                            @forelse($eventos as $evento)
                                <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded-lg transition group">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-50 flex flex-col items-center justify-center text-indigo-600">
                                        <span class="text-[10px] font-bold uppercase">{{ $evento->start_time->format('M') }}</span>
                                        <span class="text-sm font-bold">{{ $evento->start_time->format('d') }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">
                                            {{ $evento->expediente_id ? "📂 " : "👤 " }}{{ $evento->titulo }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $evento->start_time->format('H:i') }}</p>
                                    </div>
                                    <div class="flex space-x-1">
                                        <button wire:click="edit({{ $evento->id }})" class="p-1 text-indigo-600 hover:bg-indigo-50 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic">No hay eventos próximos.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <x-modal-wire wire:model="showModal">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ $editMode ? __('Editar Evento') : __('Nuevo Evento') }}
                    </h2>
                    @if($editMode)
                        <div class="flex items-center">
                            @if($eventId && \App\Models\Evento::find($eventId)?->google_event_id)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Sincronizado con Google
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    No sincronizado
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="mt-6 space-y-6">
                    <!-- Title -->
                    <div>
                        <x-input-label for="title" :value="__('Título')" />
                        <x-text-input wire:model="title" id="title" class="block mt-1 w-full" type="text" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div>
                        <x-input-label for="description" :value="__('Descripción')" />
                        <textarea wire:model="description" id="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Type -->
                    <div>
                        <x-input-label for="type" :value="__('Tipo')" />
                        <select wire:model="type" id="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="audiencia">Audiencia</option>
                            <option value="termino">Término</option>
                            <option value="cita">Cita</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <!-- Start -->
                    <div>
                        <x-input-label for="start" :value="__('Fecha y Hora Inicio')" />
                        <x-text-input wire:model="start" id="start" class="block mt-1 w-full" type="datetime-local" required />
                        <x-input-error :messages="$errors->get('start')" class="mt-2" />
                    </div>

                    <!-- End -->
                    <div>
                        <x-input-label for="end" :value="__('Fecha y Hora Fin')" />
                        <x-text-input wire:model="end" id="end" class="block mt-1 w-full" type="datetime-local" />
                        <x-input-error :messages="$errors->get('end')" class="mt-2" />
                    </div>

                    <!-- Expediente -->
                    <div>
                        <x-input-label for="expediente_id" :value="__('Expediente Relacionado')" />
                        <select wire:model="expediente_id" id="expediente_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Ninguno (Evento Personal) --</option>
                            @foreach($expedientes as $expediente)
                                <option value="{{ $expediente->id }}">{{ $expediente->numero }} - {{ $expediente->titulo }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[10px] text-gray-500 italic">
                            * Los eventos sin expediente son privados y solo tú podrás verlos. Los eventos con expediente son compartidos con el equipo asignado.
                        </p>
                        <x-input-error :messages="$errors->get('expediente_id')" class="mt-2" />
                    </div>

                    <!-- Invited Lawyers -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <x-input-label :value="__('Invitar Abogados Específicos')" class="mb-2" />
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2">
                            @foreach($abogados as $abogado)
                                <label class="flex items-center space-x-3 p-2 hover:bg-white rounded-md transition cursor-pointer border border-transparent hover:border-gray-200">
                                    <input type="checkbox" wire:model="selectedUsers" value="{{ $abogado->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700">{{ $abogado->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if(count($abogados) == 0)
                            <p class="text-xs text-gray-500 italic">No hay otros abogados disponibles para invitar.</p>
                        @endif
                        <p class="mt-2 text-[10px] text-gray-500 italic">
                            * Estos abogados recibirán una invitación en su Google Calendar independientemente de si están asignados al expediente o no.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button wire:click="$set('showModal', false)">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-primary-button class="ml-3" wire:click="{{ $editMode ? 'update' : 'store' }}">
                        {{ $editMode ? __('Actualizar') : __('Guardar') }}
                    </x-primary-button>
                </div>
            </div>
        </x-modal-wire>

        <!-- Delete Confirmation Modal -->
        <x-modal-wire wire:model="confirmingDeletion">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('¿Estás seguro de que quieres eliminar este evento?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Esta acción no se puede deshacer.') }}
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button wire:click="$set('confirmingDeletion', false)">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-danger-button class="ml-3" wire:click="delete">
                        {{ __('Eliminar') }}
                    </x-danger-button>
                </div>
            </div>
        </x-modal-wire>

    </div>
    @push('styles')
    <style>
        .fc .fc-button-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .fc .fc-button-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }
        .fc-event {
            cursor: pointer;
            padding: 2px 4px;
            border-radius: 4px;
            border: none;
        }
        @media (max-width: 767px) {
            .fc .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }
            .fc .fc-toolbar-title {
                font-size: 1rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js' data-navigate-once></script>
    @endpush
</div>
