<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">Studio de Marketing</h2>
            <p class="text-gray-500 text-sm">Crea imágenes profesionales para tus redes sociales o presentaciones.</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="bg-indigo-50 px-4 py-2 rounded-full border border-indigo-200 shadow-sm flex items-center" title="Saldo disponible">
                <svg class="w-4 h-4 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold text-indigo-700 text-sm">
                    {{ auth()->user()->tenant->marketing_credits }} Créditos
                </span>
            </div>
            <div class="text-xs text-gray-400 hidden sm:block">Powered by DALL-E 3</div>
        </div>
    </div>

    <!-- Generator Panel -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 relative">
        <!-- Loading Overlay -->
        <!-- Loading Overlay (Full Screen) -->
        <div wire:loading.flex wire:target="generate" class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-[100] flex-col items-center justify-center">
            <div class="bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center max-w-sm mx-4 animate-bounce-in">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-indigo-100 border-t-indigo-600 mb-6"></div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Creando tu Diseño...</h3>
                <p class="text-indigo-600 font-medium animate-pulse text-center">La IA está pintando tu idea pixel por pixel.</p>
                <p class="text-xs text-gray-400 mt-4 text-center">Esto toma unos 15-20 segundos.<br>Por favor no cierres esta ventana.</p>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Inputs -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Ad Toggle -->
                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <div>
                            <span class="font-bold text-gray-800">Modo Anuncio</span>
                            <p class="text-xs text-gray-500">La IA intentará integrar texto publicitario en la imagen.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="is_ad" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    @if($is_ad)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 animate-fade-in-down">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Título Principal (Headline)</label>
                            <input type="text" wire:model="headline" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400" placeholder="Ej: ¿INQUILINOS MOROSOS?">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Subtítulo (Opcional)</label>
                            <input type="text" wire:model="subheadline" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400" placeholder="Ej: Recupérala en 30 días">
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Concepto Visual (Prompt)</label>
                        <textarea 
                            wire:model="prompt"
                            rows="4"
                            class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-lg placeholder-gray-300 transition-all"
                            placeholder="Ej: Un abogado joven revisando contratos en una oficina moderna con ventanales grandes, iluminación cinematográfica, estilo fotorrealista 8k..."
                        ></textarea>
                        @error('prompt') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Estilo Visual</label>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Vivid Option -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" wire:model="style" value="vivid" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:bg-gray-50 transition-all border-gray-200">
                                    <div class="font-bold text-gray-900 mb-1">Vivido / Hiperrealista</div>
                                    <p class="text-xs text-gray-500">Colores intensos, contraste alto, ideal para impacto visual.</p>
                                </div>
                                <div class="absolute top-4 right-4 text-indigo-600 opacity-0 peer-checked:opacity-100">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </label>

                            <!-- Natural Option -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" wire:model="style" value="natural" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:bg-gray-50 transition-all border-gray-200">
                                    <div class="font-bold text-gray-900 mb-1">Natural / Fotográfico</div>
                                    <p class="text-xs text-gray-500">Iluminación suave, aspecto de fotografía documental.</p>
                                </div>
                                <div class="absolute top-4 right-4 text-indigo-600 opacity-0 peer-checked:opacity-100">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button 
                            wire:click="generate" 
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg shadow-indigo-500/30 transform hover:-translate-y-0.5 transition-all flex items-center justify-center space-x-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Generar Imagen</span>
                        </button>
                    </div>
                    
                    @if($error)
                        <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm border border-red-100 flex items-start">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $error }}
                        </div>
                    @endif
                </div>

                <!-- Preview Area -->
                <div class="md:col-span-1">
                    @if($generatedImage)
                        <div class="bg-gray-50 rounded-xl p-2 border border-dashed border-gray-300 h-full flex flex-col">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 text-center">Resultado Reciente</h3>
                            <div class="flex-grow relative group rounded-lg overflow-hidden bg-white shadow-sm">
                                <img src="{{ $generatedImage->url }}" class="w-full h-full object-cover" alt="Generado por IA">
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="download({{ $generatedImage->id }})" class="bg-white text-gray-900 px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-100 transform scale-95 hover:scale-100 transition-all">
                                        Descargar HD
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-gray-500 p-2 bg-white rounded border border-gray-100">
                                <span class="font-bold block mb-1">Prompt utilizado:</span>
                                {{ $generatedImage->revised_prompt ?? $generatedImage->prompt }}
                            </div>
                        </div>
                    @else
                        <div class="h-full border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 p-6 text-center bg-gray-50/50">
                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-sm">Tu obra maestra aparecerá aquí</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- History Gallery -->
    @if($history->isNotEmpty())
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Creaciones Recientes
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($history as $img)
                    <div class="aspect-square relative group rounded-xl overflow-hidden shadow-sm bg-gray-100 cursor-pointer border border-gray-200 hover:shadow-md transition-shadow">
                        <img src="{{ $img->url }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3">
                            <p class="text-white text-xs line-clamp-2 mb-2 font-medium">{{ $img->prompt }}</p>
                            <button wire:click="download({{ $img->id }})" class="w-full bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white text-xs py-1.5 rounded-lg border border-white/40 transition-colors">
                                Descargar
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
