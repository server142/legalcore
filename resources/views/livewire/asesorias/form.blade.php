<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $modoEdicion ? 'Gestionar Asesoría: ' . $asesoria->folio : 'Nueva Asesoría' }}
    </h2>
</x-slot>

<div class="p-4 md:p-6 max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Header del Formulario --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Datos de la Asesoría</h3>
                <p class="text-sm text-gray-500">Completa la información para agendar o gestionar la cita</p>
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
            {{-- Sección 1: Información del Prospecto --}}
            <div>
                <h4 class="text-sm font-bold text-indigo-600 uppercase mb-4 border-b pb-2">1. Información del Prospecto / Cliente</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-3">
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                            <button type="button" wire:click="$set('showClienteModal', true)" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">+ Nuevo Cliente</button>
                        </div>
                        <select wire:model.live="cliente_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccione un cliente</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                        <input wire:model="nombre_prospecto" type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nombre_prospecto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input wire:model="telefono" type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input wire:model="email" type="email" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asunto / Motivo de Consulta *</label>
                        <textarea wire:model="asunto" rows="2" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('asunto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 2: Detalles de la Cita --}}
            <div>
                <h4 class="text-sm font-bold text-indigo-600 uppercase mb-4 border-b pb-2">2. Detalles de la Cita</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                        <input wire:model="fecha" type="date" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('fecha') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                        <input wire:model="hora" type="time" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('hora') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if($suggested_fecha && $suggested_hora)
                        <div class="md:col-span-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-yellow-800">Horario sugerido</p>
                                    <p class="text-sm text-yellow-700">{{ $suggested_fecha }} {{ $suggested_hora }}</p>
                                </div>
                                <button type="button" wire:click="applySuggestedSlot" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-bold shadow-sm transition">
                                    Usar horario sugerido
                                </button>
                            </div>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duración (min)</label>
                        <select wire:model="duracion_minutos" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="15">15 min</option>
                            <option value="30">30 min</option>
                            <option value="45">45 min</option>
                            <option value="60">1 hora</option>
                            <option value="90">1.5 horas</option>
                            <option value="120">2 horas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Asesoría *</label>
                        <select wire:model.live="tipo" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="presencial">🏢 Presencial</option>
                            <option value="telefonica">📞 Telefónica</option>
                            <option value="videoconferencia">📹 Videoconferencia</option>
                        </select>
                    </div>
                    
                    @if($tipo === 'videoconferencia')
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Link de Videoconferencia</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    https://
                                </span>
                                <input wire:model="link_videoconferencia" type="text" class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="meet.google.com/abc-defg-hij">
                            </div>
                            @error('link_videoconferencia') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Abogado Asignado</label>
                        @if($isAdmin)
                            <select wire:model="abogado_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">(Se asignará al creador si no eliges)</option>
                                @foreach($abogados as $abogado)
                                    <option value="{{ $abogado->id }}">{{ $abogado->name }}</option>
                                @endforeach
                            </select>
                            @error('abogado_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @else
                            <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                Se asignará automáticamente a ti.
                            </div>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Costo ($)</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input wire:model="costo" type="number" step="0.01" class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 sm:text-sm">MXN</span>
                            </div>
                        </div>
                        @error('costo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 3: Seguimiento y Estado (Solo visible en edición o si se cambia estado) --}}
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <h4 class="text-sm font-bold text-indigo-600 uppercase mb-4 border-b pb-2 border-gray-300">3. Seguimiento y Estado</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado Actual</label>
                        <select wire:model.live="estado" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-bold">
                            <option value="agendada">📅 Agendada</option>
                            <option value="realizada">✅ Realizada</option>
                            <option value="cancelada">❌ Cancelada</option>
                            <option value="no_atendida">🚫 No Atendida (No show)</option>
                        </select>
                    </div>

                    {{-- Pago (solo si está habilitado por tenant y el usuario puede facturar) --}}
                    @if($asesoriasBillingEnabled && $canManageBilling)
                        <div class="flex items-center space-x-4 pt-6">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="pagado" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm font-bold text-gray-700">¿Asesoría Pagada?</span>
                            </label>
                            
                            @if($pagado)
                                <input wire:model="fecha_pago" type="date" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @endif

                            @if($modoEdicion && $asesoria && $asesoria->factura_id)
                                <a href="{{ route('reportes.factura', $asesoria->factura_id) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 font-bold underline">
                                    Descargar recibo (PDF)
                                </a>
                            @endif
                        </div>

                        @if($pagado)
                            @php
                                $settingsPay = auth()->user()->tenant?->settings ?? [];
                                $tBank = trim((string) ($settingsPay['payment_transfer_bank'] ?? ''));
                                $tHolder = trim((string) ($settingsPay['payment_transfer_holder'] ?? ''));
                                $tClabe = trim((string) ($settingsPay['payment_transfer_clabe'] ?? ''));
                                $tAccount = trim((string) ($settingsPay['payment_transfer_account'] ?? ''));
                                $cBank = trim((string) ($settingsPay['payment_card_bank'] ?? ''));
                                $cHolder = trim((string) ($settingsPay['payment_card_holder'] ?? ''));
                                $cNumber = trim((string) ($settingsPay['payment_card_number'] ?? ''));
                                $hasTransfer = !empty($tBank) || !empty($tHolder) || !empty($tClabe) || !empty($tAccount);
                                $hasCard = !empty($cBank) || !empty($cHolder) || !empty($cNumber);
                            @endphp

                            @if($hasTransfer || $hasCard)
                                <div class="mt-4 p-4 rounded-xl border border-emerald-100 bg-emerald-50">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-3.314 0-6 1.79-6 4s2.686 4 6 4 6-1.79 6-4-2.686-4-6-4z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12V7a2 2 0 012-2h14a2 2 0 012 2v5"/></svg>
                                        <div class="text-sm font-extrabold text-emerald-900">Formas de pago (para registrar el cobro)</div>
                                    </div>
                                    <div class="mt-1 text-xs text-emerald-800">Usa estos datos para que el cliente realice el pago.</div>

                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @if($hasTransfer)
                                            <div class="bg-white rounded-xl border border-emerald-100 p-4">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>
                                                    <div class="text-sm font-extrabold text-emerald-900">Transferencia</div>
                                                </div>
                                                <div class="mt-2 text-xs text-gray-700 space-y-1">
                                                    @if($tBank)<div><span class="font-bold">Banco:</span> {{ $tBank }}</div>@endif
                                                    @if($tHolder)<div><span class="font-bold">Titular:</span> {{ $tHolder }}</div>@endif
                                                    @if($tClabe)<div><span class="font-bold">CLABE:</span> {{ $tClabe }}</div>@endif
                                                    @if($tAccount)<div><span class="font-bold">Cuenta:</span> {{ $tAccount }}</div>@endif
                                                </div>
                                            </div>
                                        @endif

                                        @if($hasCard)
                                            <div class="bg-white rounded-xl border border-emerald-100 p-4">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 11h18M7 15h4m-7 4h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                    <div class="text-sm font-extrabold text-emerald-900">Tarjeta</div>
                                                </div>
                                                <div class="mt-2 text-xs text-gray-700 space-y-1">
                                                    @if($cBank)<div><span class="font-bold">Banco:</span> {{ $cBank }}</div>@endif
                                                    @if($cHolder)<div><span class="font-bold">Titular:</span> {{ $cHolder }}</div>@endif
                                                    @if($cNumber)<div><span class="font-bold">Tarjeta:</span> {{ $cNumber }}</div>@endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>

                {{-- Campos condicionales según estado --}}
                @if($estado === 'cancelada')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-red-700 mb-1">Motivo de Cancelación *</label>
                        <textarea wire:model="motivo_cancelacion" rows="2" class="w-full rounded-lg border-red-300 focus:border-red-500 focus:ring-red-500 bg-red-50"></textarea>
                        @error('motivo_cancelacion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                @endif

                @if($estado === 'no_atendida')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de No Atención *</label>
                        <textarea wire:model="motivo_no_atencion" rows="2" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('motivo_no_atencion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                @endif

                @if($estado === 'realizada')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Resumen / Conclusiones</label>
                            <textarea wire:model="resumen" rows="3" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="¿Qué se trató? ¿Qué se acordó?"></textarea>
                        </div>
                        
                        <div class="flex items-center space-x-6 bg-white p-3 rounded-lg border border-gray-200">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="prospecto_acepto" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm font-bold text-gray-700">¿El prospecto aceptó contratar?</span>
                            </label>
                        </div>

                        {{-- Opciones de conversión --}}
                        @if($prospecto_acepto && $modoEdicion)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                @if(!$asesoria->cliente_id)
                                    <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100 flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-bold text-indigo-800">Convertir a Cliente</p>
                                            <p class="text-xs text-indigo-600">Crear perfil de cliente automáticamente</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="crear_cliente" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>
                                @else
                                    <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                                        <p class="text-sm font-bold text-green-800">✅ Cliente Vinculado</p>
                                        <p class="text-xs text-green-600">{{ $asesoria->cliente->nombre }}</p>
                                    </div>
                                @endif

                                @if($asesoria->cliente_id && !$asesoria->expediente_id)
                                    <div class="bg-purple-50 p-3 rounded-lg border border-purple-100">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-bold text-purple-800">Crear Expediente</p>
                                                <p class="text-xs text-purple-600">Abrir nuevo expediente para este asunto</p>
                                            </div>
                                            <button type="button" wire:click="crearExpedienteDesdeAsesoria" 
                                                    class="px-4 py-2 bg-purple-600 text-white text-sm font-bold rounded-lg hover:bg-purple-700 transition-colors">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Crear
                                            </button>
                                        </div>
                                    </div>
                                @elseif($asesoria->expediente_id)
                                    <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                                        <p class="text-sm font-bold text-green-800">✅ Expediente Creado</p>
                                        <a href="{{ route('expedientes.show', $asesoria->expediente_id) }}" target="_blank" class="text-xs text-green-600 underline hover:text-green-800">Ver Expediente</a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Footer con Botones --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('asesorias.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                Cancelar
            </a>
            <button wire:click="guardar" wire:loading.attr="disabled" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold shadow-sm transition flex items-center">
                <span wire:loading.remove wire:target="guardar">Guardar Asesoría</span>
                <span wire:loading wire:target="guardar">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Guardando...
                </span>
            </button>
        </div>
    </div>

    @if($showClienteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showClienteModal', false)"></div>
                <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-50">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Nuevo Cliente</h3>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="newClienteNombre" :value="__('Nombre Completo')" />
                                <x-text-input id="newClienteNombre" type="text" class="mt-1 block w-full" wire:model="newClienteNombre" />
                                <x-input-error :messages="$errors->get('newClienteNombre')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="newClienteEmail" :value="__('Email')" />
                                <x-text-input id="newClienteEmail" type="email" class="mt-1 block w-full" wire:model="newClienteEmail" />
                                <x-input-error :messages="$errors->get('newClienteEmail')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="newClienteTelefono" :value="__('Teléfono')" />
                                <x-text-input id="newClienteTelefono" type="text" class="mt-1 block w-full" wire:model="newClienteTelefono" />
                                <x-input-error :messages="$errors->get('newClienteTelefono')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showClienteModal', false)" class="text-sm text-gray-500">Cancelar</button>
                            <x-primary-button wire:click="createCliente">Guardar Cliente</x-primary-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    window.addEventListener('asesoria-saved-receipt', (event) => {
        const data = event.detail?.[0] || event.detail || {};

        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4';

        const modal = document.createElement('div');
        modal.className = 'w-full max-w-md rounded-2xl bg-white shadow-xl border border-gray-200 overflow-hidden';

        modal.innerHTML = `
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-extrabold text-gray-900">Asesoría guardada</h3>
                        <p class="mt-1 text-sm text-gray-600">${(data.message || 'Se guardó correctamente.')}</p>
                        <p class="mt-3 text-sm font-semibold text-gray-800">¿Deseas generar el recibo ahora?</p>
                    </div>
                </div>
            </div>
            <div class="px-6 pb-6 flex justify-end gap-3">
                <button type="button" data-action="edit" class="px-4 py-2 rounded-xl bg-gray-600 text-white font-bold hover:bg-gray-700">Seguir editando</button>
                <button type="button" data-action="no" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-bold hover:bg-gray-50">Regresar a lista</button>
                <button type="button" data-action="yes" class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700">Sí, emitir recibo</button>
            </div>
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        const cleanup = () => {
            try { document.body.removeChild(overlay); } catch (e) {}
        };

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                cleanup();
                if (data.redirectUrl) window.location.href = data.redirectUrl;
            }
        });

        modal.querySelector('[data-action="edit"]').addEventListener('click', () => {
            cleanup();
            // No hace nada, se queda en la misma página
        });

        modal.querySelector('[data-action="no"]').addEventListener('click', () => {
            cleanup();
            if (data.redirectUrl) window.location.href = data.redirectUrl;
        });

        modal.querySelector('[data-action="yes"]').addEventListener('click', () => {
            cleanup();
            if (data.facturaUrl) window.open(data.facturaUrl, '_blank');
            if (data.redirectUrl) window.location.href = data.redirectUrl;
        });
    });
</script>
@endpush
