<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Top Navigation -->
        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('projects.index') }}" class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors">
                <svg class="mr-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver a Proyectos
            </a>
            <div class="flex items-center gap-2">
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Progreso</span>
                <span class="text-lg font-black text-indigo-600">{{ $this->calculateProgress() }}%</span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-10 w-full bg-gray-200 rounded-full h-2 overflow-hidden shadow-inner">
            <div class="bg-gradient-to-r from-indigo-500 to-cyan-400 h-2 rounded-full transition-all duration-700 ease-out" 
                 style="width: {{ $this->calculateProgress() }}%"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Wizard Area -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
                    <!-- Step Header -->
                    <div class="px-10 py-8 bg-slate-900 text-white">
                        <div class="flex items-center gap-4 mb-2">
                            <span class="px-3 py-1 bg-indigo-500 rounded-lg text-xs font-black uppercase">Paso {{ $currentStep }} de {{ $totalSteps }}</span>
                        </div>
                        <h3 class="text-2xl font-black">{{ $currentStepData['title'] }}</h3>
                        <p class="text-slate-400 text-sm mt-1">{{ $currentStepData['description'] }}</p>
                    </div>

                    <!-- Step Content -->
                    <div class="p-10">
                        @if(isset($currentStepData['fields']))
                            <div class="space-y-6">
                                @foreach($currentStepData['fields'] as $field)
                                    <div>
                                        <label class="block text-sm font-black text-gray-700 mb-2 uppercase tracking-tighter">{{ $field['label'] }}</label>
                                        @if($field['type'] == 'text')
                                            <input type="text" wire:model="responses.{{ $field['name'] }}" 
                                                class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
                                                placeholder="Ingresa {{ strtolower($field['label']) }}...">
                                        @elseif($field['type'] == 'number')
                                            <input type="number" wire:model="responses.{{ $field['name'] }}" 
                                                class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(isset($currentStepData['checks']))
                            <div class="space-y-3">
                                <p class="text-xs font-bold text-gray-400 uppercase mb-4">Asegúrate de tener lo siguiente:</p>
                                @foreach($currentStepData['checks'] as $index => $check)
                                    <label class="flex items-center p-4 bg-gray-50 border border-gray-100 rounded-2xl cursor-pointer hover:bg-white hover:border-indigo-200 transition-all group">
                                        <input type="checkbox" wire:model.defer="responses.check_{{ $index }}" 
                                            class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-3 text-sm font-bold text-gray-700 group-hover:text-indigo-600 transition-colors">{{ $check }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        @if(isset($currentStepData['action']) && $currentStepData['action'] == 'generate_format')
                            <div class="text-center py-10">
                                <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-10 h-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <h4 class="text-xl font-black text-gray-900 mb-2">¡Todo listo!</h4>
                                <p class="text-gray-500 text-sm mb-8 px-10">Hemos recolectado toda la información necesaria para tu {{ $workflow->name }}. Sugerimos usar el formato: <span class="font-bold text-indigo-600">{{ $currentStepData['template_suggest'] }}</span>.</p>
                                
                                <button wire:click="generateDocument" wire:loading.attr="disabled" class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all transform hover:scale-105 disabled:opacity-50">
                                    <span wire:loading.remove wire:target="generateDocument">Generar Documento Pre-llenado</span>
                                    <span wire:loading wire:target="generateDocument">Generando...</span>
                                    <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>

                                @if($project && !$project->expediente_id)
                                <div class="mt-4">
                                    <button wire:click="convertToExpediente" wire:loading.attr="disabled" class="inline-flex items-center px-8 py-3 border-2 border-slate-900 text-slate-900 rounded-2xl font-bold hover:bg-slate-900 hover:text-white transition-all disabled:opacity-50">
                                        <span wire:loading.remove wire:target="convertToExpediente">Formalizar como Nuevo Expediente</span>
                                        <span wire:loading wire:target="convertToExpediente">Creando Expediente...</span>
                                        <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </button>
                                </div>
                                @elseif($project && $project->expediente_id)
                                <div class="mt-4">
                                    <a href="{{ route('expedientes.show', $project->expediente_id) }}" class="inline-flex items-center px-8 py-3 bg-green-50 text-green-700 rounded-2xl font-bold border border-green-200 hover:bg-green-100 transition-all">
                                        Ver Expediente Vinculado
                                        <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Footer Buttons -->
                    <div class="px-10 py-6 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <button wire:click="prevStep" 
                            class="inline-flex items-center px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 bg-white hover:bg-gray-100 transition-all @if($currentStep == 1) opacity-0 pointer-events-none @endif">
                            <svg class="mr-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Anterior
                        </button>

                        @if($currentStep < $totalSteps)
                        <button wire:click="nextStep" 
                            class="inline-flex items-center px-8 py-3 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-black shadow-lg transition-all transform hover:scale-105">
                            Siguiente
                            <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar / Settings Area -->
            <div class="space-y-6">
                <div class="bg-white rounded-[2rem] p-8 shadow-xl border border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Configuración del Proyecto</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase mb-2">Título del Proyecto</label>
                            <input type="text" wire:model.defer="title" 
                                class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold shadow-sm">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest">Cliente Asignado</label>
                                <button wire:click="openCreateClienteModal" class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-tighter flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    Nuevo
                                </button>
                            </div>
                            <select wire:model.defer="cliente_id" 
                                class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold shadow-sm appearance-none">
                                <option value="">-- Seleccionar Cliente --</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-4 border-t border-gray-100 italic">
                            <p class="text-[10px] text-gray-400">Tus cambios se guardan automáticamente al avanzar de paso.</p>
                        </div>
                    </div>
                </div>

                <!-- Helper AI Card -->
                <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-[2rem] p-8 shadow-xl text-white relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 opacity-20">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    </div>
                    <h4 class="text-lg font-black mb-2">Asistente Diogenes</h4>
                    <p class="text-indigo-200 text-xs leading-relaxed mb-4">¿Necesitas ayuda con este paso? Puedo buscar jurisprudencia relevante sobre este tema para fortalecer tu demanda.</p>
                    <button wire:click="investigateIA" class="w-full py-2 bg-white/10 hover:bg-white/20 rounded-xl text-xs font-bold transition-all border border-white/20">
                        Investigar con IA
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal para Nuevo Cliente Express -->
    <x-modal-wire wire:model="showCreateClienteModal" maxWidth="md">
        <div class="p-8">
            <div class="mb-6">
                <h3 class="text-xl font-black text-gray-900">Registro Rápido de Cliente</h3>
                <p class="text-sm text-gray-500">Crea el perfil básico para este proyecto.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase mb-1">Nombre Completo</label>
                    <input type="text" wire:model.defer="newCliente.nombre" 
                        class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold shadow-sm">
                    @error('newCliente.nombre') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase mb-1">Tipo</label>
                        <select wire:model.defer="newCliente.tipo" 
                            class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold shadow-sm">
                            <option value="persona_fisica">Física</option>
                            <option value="persona_moral">Moral</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase mb-1">Teléfono</label>
                        <input type="text" wire:model.defer="newCliente.telefono" 
                            class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase mb-1">Correo Electrónico</label>
                    <input type="email" wire:model.defer="newCliente.email" 
                        class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold shadow-sm">
                    @error('newCliente.email') <span class="text-red-500 text-[10px] font-bold uppercase">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <button wire:click="$set('showCreateClienteModal', false)" 
                    class="flex-1 px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 transition-all">
                    Cancelar
                </button>
                <button wire:click="saveNewCliente" 
                    class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                    Guardar y Asignar
                </button>
            </div>
        </div>
    </x-modal-wire>
</div>
