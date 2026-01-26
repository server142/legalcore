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
                                        <p class="text-xs text-gray-500">{{ $evento->start_time->format('H:i') }}</p>
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
                <h2 class="text-lg font-medium text-gray-900">
                    {{ $editMode ? __('Editar Evento') : __('Nuevo Evento') }}
                </h2>

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
