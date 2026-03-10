<div class="bg-white p-4 rounded-xl border-2 border-dashed border-indigo-200 hover:border-indigo-400 transition-all group relative"
     x-data="{ isDropping: false, progress: 0 }"
     x-on:dragover.prevent="isDropping = true"
     x-on:dragleave.prevent="isDropping = false"
     x-on:drop.prevent="isDropping = false; $wire.upload('files', $event.dataTransfer.files)"
     x-on:livewire-upload-start="progress = 0"
     x-on:livewire-upload-finish="progress = 100"
     x-on:livewire-upload-error="progress = 0"
     x-on:livewire-upload-progress="progress = $event.detail.progress"
     :class="{ 'bg-indigo-50 border-indigo-500': isDropping }">
    
    @if (session()->has('error'))
        <div class="absolute top-2 left-2 right-2 z-20 bg-red-100 border border-red-400 text-red-700 px-3 py-1 rounded text-[10px] shadow-sm flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button @click="this.parentElement.remove()" class="text-red-900 font-bold ml-2">&times;</button>
        </div>
    @endif

    <div class="text-center py-2">
        <div class="bg-indigo-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
        </div>
        
        <h4 class="text-sm font-bold text-gray-800">Arrastra y suelta archivos aquí</h4>
        <p class="text-xs text-gray-500 mb-2">O haz clic para buscar en tu equipo</p>
        
        <label class="cursor-pointer bg-indigo-600 text-white px-4 py-1.5 rounded-full text-xs font-semibold hover:bg-indigo-700 transition shadow-sm inline-block">
            Seleccionar
            <input type="file" wire:model="files" multiple class="hidden">
        </label>

        <!-- Barra de Progreso de Carga (Livewire Upload) -->
        <div x-show="progress > 0 && progress < 100" class="mt-4 px-10">
            <div class="bg-gray-200 rounded-full h-1.5 mb-1">
                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
            </div>
            <span class="text-[10px] font-bold text-indigo-600" x-text="'Anexando archivos: ' + progress + '%'"></span>
        </div>

        @if($files)
            <div class="mt-4 text-left border-t pt-3">
                <div class="flex justify-between items-center mb-2">
                    <h5 class="text-[10px] font-bold text-gray-400 uppercase">Archivos por procesar ({{ count($files) }}):</h5>
                    <button wire:click="save" class="bg-green-600 text-white px-3 py-1 rounded text-[10px] font-bold hover:bg-green-700 transition flex items-center shadow-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Confirmar Subida
                    </button>
                </div>
                <div class="max-h-32 overflow-y-auto space-y-1 pr-1 custom-scrollbar">
                    @foreach($files as $file)
                        <div class="flex items-center justify-between bg-gray-50 p-1.5 rounded border border-gray-100">
                            <div class="flex items-center space-x-2 overflow-hidden">
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="text-[10px] text-gray-700 truncate">{{ $file->getClientOriginalName() }}</span>
                            </div>
                            <span class="text-[9px] text-gray-400">{{ number_format($file->getSize() / 1024, 1) }} KB</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Overlay de Guardado -->
        <div wire:loading wire:target="save, files" class="absolute inset-0 bg-white bg-opacity-90 flex flex-col items-center justify-center rounded-xl z-10">
            <div class="relative">
                <svg class="animate-spin h-10 w-10 text-indigo-600 mb-2" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-[8px] font-bold text-indigo-600" x-show="progress > 0" x-text="progress + '%'"></span>
                </div>
            </div>
            <span class="text-xs font-bold text-indigo-600 animate-pulse">Procesando archivos...</span>
            <p class="text-[9px] text-gray-400 mt-1">Por favor, no cierres esta ventana</p>
        </div>

        @error('files.*') <p class="mt-1 text-[10px] text-red-500">{{ $message }}</p> @enderror
    </div>
</div>
