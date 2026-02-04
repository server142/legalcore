<div class="max-w-4xl w-full bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200 mx-auto transform transition-all">
    <!-- Header with proper contrast -->
    <div class="bg-gray-100 px-8 py-6 text-center border-b border-gray-200">
        <h2 class="text-2xl font-bold tracking-tight text-indigo-900">Revisión de Documento Legal</h2>
        <p class="text-indigo-600 text-sm mt-2 font-medium">Acción requerida para continuar en el sistema</p>
    </div>

    <!-- Main Content Area -->
    <div class="p-8 space-y-6">
        
        <!-- Document Title & Version Bubble -->
        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
            <h3 class="text-xl font-extrabold text-gray-800">{{ $pendingDocs[0]->nombre ?? 'Documento Legal' }}</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                v{{ $pendingDocs[0]->version ?? '1.0' }}
            </span>
        </div>

        <!-- Scrollable Document Text -->
        <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-inner max-h-[50vh] overflow-y-auto custom-scrollbar text-justify ring-1 ring-black/5">
            <div class="prose prose-sm prose-slate max-w-none text-gray-600 leading-relaxed">
                @if(isset($pendingDocs[0]))
                    @if(str_contains($pendingDocs[0]->texto, '<p>') || str_contains($pendingDocs[0]->texto, '<div>'))
                        {!! $pendingDocs[0]->texto !!}
                    @else
                        {!! \Illuminate\Support\Str::markdown($pendingDocs[0]->texto) !!}
                    @endif
                @endif
            </div>
        </div>

        <!-- Acceptance Checkbox Area -->
        <div class="mt-6 pt-6 border-t border-gray-100">
            @if(isset($pendingDocs[0]))
                <label for="doc-{{ $pendingDocs[0]->id }}" 
                       class="flex items-center p-4 rounded-lg border-2 cursor-pointer transition-all select-none group {{ ($accepted[$pendingDocs[0]->id] ?? false) ? 'border-indigo-500 bg-indigo-50/50' : 'border-gray-200 hover:border-indigo-200 hover:bg-gray-50' }}">
                    
                    <div class="relative flex items-center h-6">
                        <input type="checkbox" 
                               wire:model.live="accepted.{{ $pendingDocs[0]->id }}" 
                               id="doc-{{ $pendingDocs[0]->id }}" 
                               class="w-6 h-6 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 transition duration-150 ease-in-out cursor-pointer">
                    </div>
                    
                    <div class="ml-4 flex-1">
                        <span class="block text-sm font-bold {{ ($accepted[$pendingDocs[0]->id] ?? false) ? 'text-indigo-900' : 'text-gray-700 group-hover:text-indigo-900' }}">
                            He leído, comprendo y acepto íntegramente el contenido de este documento.
                        </span>
                        @error('accepted.'.$pendingDocs[0]->id)
                            <p class="text-red-600 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </label>
            @endif
        </div>

        <!-- Action Footer -->
        <div class="flex flex-col items-center justify-center pt-8 gap-6">
            <button wire:click="accept" 
                    wire:loading.attr="disabled" 
                    class="w-full sm:w-auto min-w-[300px] px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-base uppercase tracking-widest rounded-lg shadow-lg shadow-indigo-200 hover:shadow-xl hover:shadow-indigo-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 transition-all transform active:scale-95 disabled:opacity-75 disabled:cursor-not-allowed">
                <span wire:loading.remove>Confirmar y Aceptar</span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando...
                </span>
            </button>
            
            <div class="text-center">
                <livewire:layout.logout-button />
            </div>
        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f3f4f6; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 4px; border: 2px solid #f3f4f6; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</div>
