<x-slot name="header">
    <x-header 
        title="{{ $modoEdicion ? 'Gestionar Asesoría' : 'Nueva Asesoría' }}" 
        subtitle="{{ $modoEdicion ? 'Folio: ' . $asesoria->folio : 'Registro de nuevo prospecto y cita' }}" 
    />
</x-slot>

<div class="p-4 md:p-6 max-w-5xl mx-auto" x-data="{ saving: false }">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
        {{-- Overlay de Carga --}}
        <div wire:loading wire:target="guardar" class="absolute inset-0 bg-white/50 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="flex flex-col items-center">
                <svg class="animate-spin h-12 w-12 text-indigo-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-indigo-900 font-black uppercase tracking-widest text-sm">Guardando Información...</p>
            </div>
        </div>

        {{-- Header del Formulario --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('asesorias.index') }}" class="p-1.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition text-gray-500 flex-shrink-0 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h3 class="text-lg font-black text-gray-900">{{ $modoEdicion ? 'Actualizar Asesoría' : 'Nueva Asesoría' }}</h3>
                    <p class="text-xs text-gray-500">{{ $modoEdicion ? 'Folio: ' . $asesoria->folio : 'Registro de nuevo prospecto y cita' }}</p>
                </div>
            </div>
            @if($modoEdicion)
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase
                        {{ $estado === 'agendada' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $estado === 'realizada' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $estado === 'cancelada' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $estado === 'no_atendida' ? 'bg-gray-100 text-gray-800' : '' }}
                    ">
                        {{ ucfirst(str_replace('_', ' ', $estado)) }}
                    </span>
                </div>
            @endif
        </div>

        <div class="p-6 space-y-8">
            {{-- Errores de Validación (MUY VISIBLE) --}}
            @if ($errors->any())
                <div id="error-summary" class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-md border border-red-100">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-black text-red-800 uppercase tracking-tight">Atención: No se pudo guardar</h3>
                            <p class="text-xs text-red-600 mt-1 mb-2">Por favor corrige los siguientes errores para continuar:</p>
                            <div class="mt-2 text-xs text-red-700 space-y-1 bg-white/50 p-2 rounded-lg">
                                @foreach ($errors->all() as $error)
                                    <p class="flex items-center">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2"></span>
                                        {{ $error }}
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Sección 1: Información del Prospecto --}}
            <div>
                <h4 class="text-sm font-bold text-indigo-600 uppercase mb-4 border-b pb-2 flex items-center">
                    <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px] mr-2">1</span>
                    Información del Prospecto / Cliente
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-3">
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl border border-gray-100 mb-2">
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase">¿Es un cliente existente?</label>
                                <p class="text-[10px] text-gray-400">Si ya está registrado, selecciónalo aquí</p>
                            </div>
                            <button type="button" wire:click="$set('showClienteModal', true)" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-xs font-black transition flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                NUEVO CLIENTE
                            </button>
                        </div>
                        <select wire:model.live="cliente_id" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-bold text-sm">
                            <option value="">-- Selecciona un Cliente del Sistema o deja vacío para Prospecto --</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Nombre Completo *</label>
                        <input wire:model="nombre_prospecto" type="text" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-bold text-sm" placeholder="Ej. Juan Pérez">
                        @error('nombre_prospecto') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Teléfono</label>
                        <input wire:model="telefono" type="text" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-bold text-sm" placeholder="10 dígitos">
                        @error('telefono') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Email</label>
                        <input wire:model="email" type="email" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-bold text-sm" placeholder="correo@ejemplo.com">
                        @error('email') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Asunto / Motivo de Consulta *</label>
                        <textarea wire:model="asunto" rows="2" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-bold text-sm" placeholder="Describe brevemente el motivo..."></textarea>
                        @error('asunto') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 2: Detalles de la Cita --}}
            <div>
                <h4 class="text-sm font-bold text-indigo-600 uppercase mb-4 border-b pb-2 flex items-center">
                    <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px] mr-2">2</span>
                    Detalles de la Cita
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Fecha *</label>
                        <input wire:model="fecha" type="date" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-bold text-sm">
                        @error('fecha') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Hora *</label>
                        <input wire:model="hora" type="time" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-bold text-sm">
                        @error('hora') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if($suggested_fecha && $suggested_hora)
                        <div class="md:col-span-4 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-black text-amber-900">⚠️ Conflicto de Horario</p>
                                <p class="text-xs text-amber-800 font-medium">El abogado ya tiene compromisos en esa hora. Sugerimos:</p>
                                <p class="text-sm font-black text-amber-700 mt-1">{{ Carbon\Carbon::parse($suggested_fecha)->format('d/m/Y') }} a las {{ $suggested_hora }}</p>
                            </div>
                            <button type="button" wire:click="applySuggestedSlot" class="px-5 py-2.5 bg-amber-600 text-white rounded-xl hover:bg-amber-700 font-black text-xs shadow-lg shadow-amber-200 transition-all flexitems-center">
                                USAR ESTE HORARIO
                            </button>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Duración</label>
                        <select wire:model="duracion_minutos" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-bold text-sm">
                            <option value="15">15 min</option>
                            <option value="30">30 min</option>
                            <option value="45">45 min</option>
                            <option value="60">1 hora</option>
                            <option value="90">1.5 horas</option>
                            <option value="120">2 horas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Modalidad *</label>
                        <select wire:model.live="tipo" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-bold text-sm">
                            <option value="presencial">🏢 Presencial</option>
                            <option value="telefonica">📞 Telefónica</option>
                            <option value="videoconferencia">📹 Videoconferencia</option>
                        </select>
                    </div>
                    
                    @if($tipo === 'videoconferencia')
                        <div class="md:col-span-4">
                            <label class="block text-xs font-black text-gray-500 uppercase mb-1">Enlace de la reunión</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-200 bg-gray-50 text-gray-400 text-xs font-bold">
                                    HTTPS://
                                </span>
                                <input wire:model="link_videoconferencia" type="text" class="flex-1 block w-full rounded-none rounded-r-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-bold text-sm" placeholder="meet.google.com/xyz-abc">
                            </div>
                            @error('link_videoconferencia') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Abogado Responsable</label>
                        @if($isAdmin)
                            <select wire:model="abogado_id" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-bold text-sm">
                                <option value="">-- Elige un abogado --</option>
                                @foreach($abogados as $abogado)
                                    <option value="{{ $abogado->id }}">{{ $abogado->name }}</option>
                                @endforeach
                            </select>
                            @error('abogado_id') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                        @else
                            <div class="w-full rounded-xl border border-gray-100 bg-gray-50 px-4 py-2.5 text-sm font-bold text-gray-600 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Se asignará a tu agenda
                            </div>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Costo de la Asesoría ($)</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-gray-900 font-black sm:text-sm">$</span>
                            </div>
                            <input wire:model="costo" type="number" step="0.01" class="block w-full rounded-xl border-gray-200 pl-8 pr-12 focus:border-indigo-500 focus:ring-indigo-500 font-black text-sm" placeholder="0.00">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                <span class="text-gray-400 font-bold sm:text-[10px]">MXN</span>
                            </div>
                        </div>
                        @error('costo') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 3: Seguimiento --}}
            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 shadow-inner">
                <h4 class="text-sm font-bold text-indigo-600 uppercase mb-4 flex items-center">
                    <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px] mr-2">3</span>
                    Seguimiento y Registro
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Estado de la Cita</label>
                        <select wire:model.live="estado" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-black text-sm uppercase tracking-tighter">
                            <option value="agendada">📅 Agendada</option>
                            <option value="realizada">✅ Realizada</option>
                            <option value="cancelada">❌ Cancelada</option>
                            <option value="no_atendida">🚫 No Atendida (No show)</option>
                        </select>
                    </div>

                    {{-- Pago --}}
                    @if($asesoriasBillingEnabled && $canManageBilling)
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="checkbox" wire:model.live="pagado" class="w-5 h-5 rounded-lg border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                    <span class="ml-3 text-sm font-black text-gray-700 uppercase tracking-tighter group-hover:text-indigo-600">¿Asesoría Pagada?</span>
                                </label>
                                
                                @if($pagado)
                                    <input wire:model="fecha_pago" type="date" class="rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold ml-4">
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                @if($estado === 'cancelada')
                    <div class="mt-6 p-4 bg-red-50 rounded-xl border border-red-100">
                        <label class="block text-xs font-black text-red-700 uppercase mb-1">Motivo de Cancelación *</label>
                        <textarea wire:model="motivo_cancelacion" rows="2" class="w-full rounded-xl border-red-200 focus:border-red-500 focus:ring-red-500 bg-white text-sm font-bold"></textarea>
                        @error('motivo_cancelacion') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                @endif

                @if($estado === 'no_atendida')
                    <div class="mt-6 p-4 bg-gray-100 rounded-xl border border-gray-200">
                        <label class="block text-xs font-black text-gray-600 uppercase mb-1">Notas de No Atención *</label>
                        <textarea wire:model="motivo_no_atencion" rows="2" class="w-full rounded-xl border-gray-300 focus:border-gray-500 focus:ring-gray-500 bg-white text-sm font-bold"></textarea>
                        @error('motivo_no_atencion') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                @endif

                @if($estado === 'realizada')
                    <div class="mt-6 space-y-6">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase mb-1">Resumen / Conclusiones de la Consulta</label>
                            <textarea wire:model="resumen" rows="3" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 bg-white text-sm font-bold" placeholder="¿Qué se acordó?"></textarea>
                        </div>
                        
                        <div class="bg-indigo-900 rounded-2xl p-5 text-white shadow-xl shadow-indigo-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-700 rounded-xl flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-black uppercase tracking-widest">¿Cierre de Venta?</h5>
                                        <p class="text-[10px] text-indigo-300 font-bold">Activa esto para convertir al prospecto en cliente o crear un expediente</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer scale-110">
                                    <input type="checkbox" wire:model.live="prospecto_acepto" class="sr-only peer">
                                    <div class="w-11 h-6 bg-indigo-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-indigo-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                                </label>
                            </div>

                            @if($prospecto_acepto && $modoEdicion)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 border-t border-indigo-800 pt-6">
                                    @if(!$asesoria->cliente_id)
                                        <div class="bg-indigo-800/50 p-4 rounded-xl flex items-center justify-between border border-indigo-700">
                                            <div>
                                                <p class="text-xs font-black uppercase tracking-tighter">Convertir a Cliente</p>
                                                <p class="text-[10px] text-indigo-300">Crear perfil definitivo</p>
                                            </div>
                                            <input type="checkbox" wire:model="crear_cliente" class="w-6 h-6 rounded-lg text-green-500 bg-indigo-900 border-indigo-700">
                                        </div>
                                    @endif

                                    @if($asesoria->cliente_id && !$asesoria->expediente_id)
                                        <div class="bg-indigo-800/50 p-4 rounded-xl flex items-center justify-between border border-indigo-700">
                                            <div>
                                                <p class="text-xs font-black uppercase tracking-tighter">Crear Expediente</p>
                                                <p class="text-[10px] text-indigo-300">Iniciar trámite legal</p>
                                            </div>
                                            <button type="button" wire:click="crearExpedienteDesdeAsesoria" class="px-3 py-1.5 bg-green-500 text-white rounded-lg text-[10px] font-black hover:bg-green-600 transition-all shadow-lg shadow-green-900/50">
                                                CREAR AHORA
                                            </button>
                                        </div>
                                    @elseif($asesoria->expediente_id)
                                         <div class="bg-green-500/20 p-4 rounded-xl border border-green-500/30 flex items-center justify-between">
                                            <div>
                                                <p class="text-xs font-black uppercase tracking-tighter text-green-300">Expediente Generado</p>
                                                <p class="text-[10px] text-green-400">Vinculado exitosamente</p>
                                            </div>
                                            <a href="{{ route('expedientes.show', $asesoria->expediente_id) }}" target="_blank" class="text-[10px] font-black text-white underline decoration-white/30 hover:text-green-200">VER EXPEDIENTE</a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Footer con Botones --}}
        <div class="px-6 py-5 bg-gray-50 border-t border-gray-200 flex justify-end items-center space-x-4">
            <div wire:loading wire:target="guardar" class="text-[10px] text-indigo-600 font-black uppercase tracking-widest animate-pulse mr-4">
                Sincronizando con base de datos...
            </div>
            
            <a href="{{ route('asesorias.index') }}" class="px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-100 font-bold text-xs uppercase tracking-widest transition-all active:scale-95 shadow-sm">
                Cancelar
            </a>
            
            <button wire:click="guardar" wire:loading.attr="disabled" class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 transition-all flex items-center active:scale-95 group">
                <span wire:loading.remove wire:target="guardar">GUARDAR ASESORÍA</span>
                <span wire:loading wire:target="guardar" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    PROCESANDO...
                </span>
                <svg wire:loading.remove wire:target="guardar" class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>
    </div>

    {{-- Modal: Registro de Cliente Rápido --}}
    @if($showClienteModal)
        <div class="fixed inset-0 z-[100] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 py-12">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showClienteModal', false)"></div>
                
                <div class="bg-white rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:max-w-2xl sm:w-full z-[110] border border-gray-100">
                    <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-600 text-white rounded-2xl flex items-center justify-center mr-4 shadow-lg shadow-indigo-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter">Alta Rápida de Cliente</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Sincronización instantánea</p>
                            </div>
                        </div>
                        <button wire:click="$set('showClienteModal', false)" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-500 uppercase mb-1.5 ml-1">Nombre Completo / Razón Social *</label>
                                <input wire:model="newClienteNombre" type="text" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold p-3.5 bg-gray-50/50">
                                @error('newClienteNombre') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase mb-1.5 ml-1">Tipo de Persona</label>
                                <select wire:model="newClienteTipo" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold p-3.5 bg-gray-50/50">
                                    <option value="persona_fisica">Persona Física</option>
                                    <option value="persona_moral">Persona Moral</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase mb-1.5 ml-1">RFC</label>
                                <input wire:model="newClienteRFC" type="text" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold p-3.5 bg-gray-50/50" placeholder="12 o 13 dígitos">
                                @error('newClienteRFC') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase mb-1.5 ml-1">Email Principal</label>
                                <input wire:model="newClienteEmail" type="email" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold p-3.5 bg-gray-50/50" placeholder="correo@ejemplo.com">
                                @error('newClienteEmail') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase mb-1.5 ml-1">Teléfono Móvil</label>
                                <input wire:model="newClienteTelefono" type="text" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold p-3.5 bg-gray-50/50" placeholder="Personal o Trabajo">
                                @error('newClienteTelefono') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-gray-500 uppercase mb-1.5 ml-1">Dirección Fiscal / Particular</label>
                                <textarea wire:model="newClienteDireccion" rows="2" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-bold p-3.5 bg-gray-50/50" placeholder="Calle, Número, Colonia, CP..."></textarea>
                                @error('newClienteDireccion') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-8">
                            <button type="button" wire:click="$set('showClienteModal', false)" class="px-6 py-3 text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-all">
                                Cancelar
                            </button>
                            <button wire:click="createCliente" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl hover:bg-indigo-700 font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 transition-all flex items-center active:scale-95 group">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                REGISTRAR CLIENTE
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('notify-error', (event) => {
            const errorDiv = document.getElementById('error-summary');
            if (errorDiv) {
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
</script>
@endpush
