<div x-data="{ open: @entangle('isOpen') }">
    <!-- Overlay -->
    <div x-show="open" style="display: none;" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        
        <!-- Modal Panel -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             @click.away="@this.closeModal()"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden relative">
            
            <!-- Header -->
            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <div>
                    <h3 class="text-lg font-black tracking-tight">Solicitud de Cita</h3>
                    <p class="text-indigo-100 text-sm font-medium">con {{ $lawyerName }}</p>
                </div>
                <button wire:click="closeModal" class="text-indigo-200 hover:text-white transition-colors p-1 bg-white/10 rounded-full hover:bg-white/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-6 max-h-[80vh] overflow-y-auto">
                
                @if($success)
                    <!-- Estado de Éxito -->
                    <div class="text-center py-10" x-data="{ fired: false }" x-init="setTimeout(() => fired = true, 100)">
                        <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-6" 
                             :class="fired ? 'scale-100 opacity-100' : 'scale-50 opacity-0'" style="transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);">
                            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h4 class="text-2xl font-black text-slate-800 mb-2">¡Solicitud Enviada!</h4>
                        <p class="text-slate-500 mb-8 max-w-sm mx-auto">Tu cita ha sido pre-agendada. El despacho ha sido notificado y se comunicarán contigo a la brevedad para confirmar.</p>
                        <button wire:click="closeModal" class="px-6 py-2.5 bg-slate-900 text-white font-bold rounded-xl shadow-lg hover:bg-slate-800 transition-colors">
                            Entendido, cerrar
                        </button>
                    </div>
                @else
                    <!-- Formulario -->
                    <form wire:submit.prevent="submit" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            
                            <!-- Nombre -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-1.5">Nombre Completo</label>
                                <input type="text" wire:model="nombre" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-shadow text-sm" placeholder="Ej. Juan Pérez">
                                @error('nombre') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-1.5">Teléfono / WhatsApp</label>
                                <input type="text" wire:model="telefono" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-shadow text-sm" placeholder="Ej. 55 1234 5678">
                                @error('telefono') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-1.5">Correo Electrónico</label>
                                <input type="email" wire:model="email" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-shadow text-sm" placeholder="tu@correo.com">
                                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Fecha -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-1.5">Fecha Preferida</label>
                                <input type="date" wire:model="fecha" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-shadow text-sm">
                                @error('fecha') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Hora y Tipo -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-1.5">Hora</label>
                                    <input type="time" wire:model="hora" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-shadow text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-1.5">Medio</label>
                                    <select wire:model="tipo" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-shadow text-sm">
                                        <option value="videoconferencia">Videollamada</option>
                                        <option value="telefonica">Teléfono</option>
                                        <option value="presencial">Presencial</option>
                                    </select>
                                </div>
                            </div>
                            @error('hora') <div class="col-span-2 text-red-500 text-xs">{{ $message }}</div> @enderror

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-1.5">Resumen del Asunto</label>
                                <textarea wire:model="asunto" rows="3" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-shadow text-sm resize-none" placeholder="Cuéntale al abogado brevemente sobre tu caso para que esté preparado..."></textarea>
                                @error('asunto') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-4 mt-6 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-medium">Información protegida con cifrado SSL</span>
                            <div class="flex gap-3 text-right">
                                <button type="button" wire:click="closeModal" class="px-5 py-2.5 text-slate-600 font-bold hover:bg-slate-50 rounded-xl transition-colors text-sm">
                                    Cancelar
                                </button>
                                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center gap-2">
                                    <svg wire:loading.remove wire:target="submit" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    <svg wire:loading wire:target="submit" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span wire:loading.remove wire:target="submit">Solicitar Cita</span>
                                    <span wire:loading wire:target="submit">Enviando...</span>
                                </button>
                            </div>
                        </div>

                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
