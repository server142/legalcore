<div class="space-y-6">
    <!-- Header Section (Refined for Congruency) -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Biblioteca de Formatos</h1>
            <p class="text-slate-500 text-sm mt-1 uppercase font-bold tracking-widest opacity-70">Repositorio Inteligente de Documentos</p>
        </div>
        <button wire:click="openUploadModal" 
                class="flex items-center justify-center gap-2 px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-500/30 transition-all group">
            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Subir Formato</span>
        </button>
    </div>

    <!-- Search Hero Section (Cleaner & Integrated) -->
    <div class="relative bg-gradient-to-br from-indigo-900 to-slate-900 rounded-[2rem] p-8 md:p-12 overflow-hidden shadow-xl border border-white/10">
        <!-- Abstract Decoration -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-500/5 rounded-full blur-[100px]"></div>

        <div class="relative z-10 max-w-2xl mx-auto text-center space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-indigo-300 text-[10px] font-bold uppercase tracking-[0.2em] backdrop-blur-sm mx-auto">
                <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Powered by Legal AI
            </div>
            <h2 class="text-white text-xl md:text-2xl font-bold">¿Qué documento necesitas hoy?</h2>
            
            <div class="relative max-w-xl mx-auto">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       class="block w-full pl-12 pr-4 py-4.5 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-indigo-300/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white/20 transition-all backdrop-blur-md shadow-2xl"
                       placeholder="Buscar por concepto (ej. pensión alimenticia)...">
            </div>
        </div>
    </div>

    <!-- Filters & Results Layout -->
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full lg:w-64 space-y-6">
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Categorías</h3>
                <div class="space-y-2">
                    <button wire:click="selectCategory('Todos')" 
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all {{ $selectedCategory == 'Todos' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100' }}">
                        <span>Todos</span>
                        <span class="text-[10px] opacity-60">Total</span>
                    </button>
                    @foreach($categories as $category)
                        <button wire:click="selectCategory('{{ $category }}')" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all {{ $selectedCategory == $category ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100' }}">
                            <span>{{ $category }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Stats Mini-Widget -->
            <div class="p-5 bg-indigo-50 rounded-2xl border border-indigo-100">
                <p class="text-indigo-900 font-bold text-sm">Biblioteca Global</p>
                <p class="text-indigo-600/70 text-xs mt-1">Acceso a más de 500 formatos base validados jurídicamente.</p>
            </div>
        </aside>

        <!-- Templates Grid -->
        <div class="flex-1 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">
                @forelse($templates as $template)
                    <div wire:click="selectTemplate({{ $template->id }})" 
                         class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-indigo-300 hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer relative overflow-hidden flex flex-col justify-between h-48">
                        
                        <!-- Left Accent Line -->
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500 group-hover:w-2 transition-all"></div>

                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <div class="w-10 h-10 rounded-lg bg-slate-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                                    @if($template->category == 'Contratos')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    @endif
                                </div>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors uppercase tracking-widest">{{ $template->materia }}</span>
                            </div>

                            <div>
                                <h3 class="font-extrabold text-slate-800 leading-tight group-hover:text-indigo-600 transition-colors">{{ $template->name }}</h3>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2 max-w-xs">{{ $template->description }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $template->is_global ? 'bg-emerald-400' : 'bg-indigo-400' }}"></span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">{{ $template->is_global ? 'Global' : 'Privado' }}</span>
                            </div>
                            <span class="text-[10px] text-slate-300 font-medium">{{ $template->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <h3 class="text-slate-800 font-bold">Sin resultados</h3>
                        <p class="text-slate-500 text-sm">Ajusta los filtros o intenta con otras palabras.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-8">
                {{ $templates->links() }}
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-show="$wire.showUploadModal" 
         x-on:keydown.escape.window="$wire.showUploadModal = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="$wire.showUploadModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

            <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <div class="px-8 pt-8 pb-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Subir Nuevo Formato</h3>
                            <p class="text-slate-500 text-sm mt-1">Usa <span class="text-indigo-600 font-bold">[CORCHETES]</span> para crear variables autocompletables.</p>
                        </div>
                        <button @click="$wire.showUploadModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveTemplate" class="space-y-5">
                        <div class="space-y-1">
                            <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest pl-1">Nombre del Formato</label>
                            <input type="text" wire:model="newTemplateName" 
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('newTemplateName') border-red-500 @enderror" 
                                   placeholder="Ej: Contrato de Arrendamiento 2024">
                            @error('newTemplateName') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest pl-1">Categoría</label>
                                <select wire:model="newTemplateCategory" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm font-medium @error('newTemplateCategory') border-red-500 @enderror">
                                    <option value="">Seleccionar...</option>
                                    <option value="Contratos">Contratos</option>
                                    <option value="Corporativo">Corporativo</option>
                                    <option value="Familiar">Familiar</option>
                                    <option value="Laboral">Laboral</option>
                                    <option value="Penal">Penal</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                @error('newTemplateCategory') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest pl-1">Materia</label>
                                <input type="text" wire:model="newTemplateMateria" 
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm @error('newTemplateMateria') border-red-500 @enderror" 
                                       placeholder="Ej: Civil">
                                @error('newTemplateMateria') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-1 text-center">
                            <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-2xl bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300 transition-all cursor-pointer relative group">
                                <input type="file" wire:model="newTemplateFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 group-hover:text-indigo-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-slate-600">
                                        <span class="relative cursor-pointer rounded-md font-bold text-indigo-600 hover:text-indigo-500">
                                            {{ $newTemplateFile ? 'Archivo seleccionado' : 'Subir un archivo' }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        {{ $newTemplateFile ? $newTemplateFile->getClientOriginalName() : 'DOCX, PDF, TXT hasta 10MB' }}
                                    </p>
                                </div>
                            </div>
                            @error('newTemplateFile') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-500/30 transition-all flex items-center justify-center gap-2">
                                <span wire:loading.remove>Guardar en la Biblioteca</span>
                                <span wire:loading>Procesando...</span>
                                <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Panel (Integrated Drawer) -->
    <div x-show="$wire.showPreview" 
         class="fixed inset-0 z-[100] flex justify-end"
         x-cloak>
        
        <!-- Backdrop -->
        <div x-show="$wire.showPreview" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$wire.closePreview()"
             class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        
        <!-- Panel -->
        <div class="relative w-full max-w-full lg:max-w-2xl bg-white h-full shadow-2xl flex flex-col overflow-hidden"
             x-show="$wire.showPreview"
             x-transition:enter="transition-transform ease-out duration-500"
             x-transition:enter-start="translate-y-full lg:translate-y-0 lg:translate-x-full"
             x-transition:enter-end="translate-y-0 lg:translate-x-0"
             x-transition:leave="transition-transform ease-in duration-500"
             x-transition:leave-start="translate-y-0 lg:translate-x-0"
             x-transition:leave-end="translate-y-full lg:translate-y-0 lg:translate-x-full">
            
            @if($selectedTemplate)
            <div class="flex flex-col h-full">
                <!-- Preview Header -->
                <div class="sticky top-0 z-20 p-6 lg:p-8 border-b border-slate-100 flex justify-between items-start bg-white/90 backdrop-blur-md">
                    <div class="pr-8">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-600 text-[9px] font-extrabold uppercase tracking-widest">{{ $selectedTemplate->category }}</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-600 text-[9px] font-extrabold uppercase tracking-widest">{{ $selectedTemplate->materia }}</span>
                        </div>
                        <h2 class="text-xl lg:text-2xl font-extrabold text-slate-900 leading-tight">{{ $selectedTemplate->name }}</h2>
                    </div>
                    <button wire:click="closePreview" class="p-2 hover:bg-slate-100 rounded-full transition-colors shrink-0">
                        <svg class="w-6 h-6 text-slate-400 hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Preview Body -->
                <div class="flex-1 overflow-y-auto p-6 lg:p-8 space-y-8 custom-scrollbar">
                    <div class="space-y-3">
                        <h3 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Descripción</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $selectedTemplate->description }}</p>
                    </div>

                    @if(!empty($selectedTemplate->placeholders) && count($selectedTemplate->placeholders) > 0)
                    <div class="space-y-4">
                        <h3 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Variables Detectadas</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedTemplate->placeholders as $ph)
                                <span class="px-3 py-1.5 bg-indigo-50 text-indigo-700 text-[11px] font-bold rounded-xl border border-indigo-100 shadow-sm">{{ $ph }}</span>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium italic">* Estas variables serán completadas por la IA al generar el documento.</p>
                    </div>
                    @endif

                    <div class="space-y-4">
                        <h3 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Vista Previa del Contenido</h3>
                        <div class="p-5 lg:p-8 bg-slate-950 rounded-2xl text-[12px] font-mono text-indigo-200/80 leading-relaxed whitespace-pre-wrap select-none shadow-inner min-h-[500px] border border-slate-800">
                            @if(empty($selectedTemplate->extracted_text))
                                <div class="flex flex-col items-center justify-center py-32 text-center space-y-4">
                                    <div class="w-16 h-16 bg-slate-900 rounded-full flex items-center justify-center animate-pulse">
                                        <svg class="w-8 h-8 text-indigo-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="text-slate-500 max-w-[200px] mx-auto">No se detectó contenido de texto en este archivo.</p>
                                </div>
                            @else
                                {{ $selectedTemplate->extracted_text }}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Preview Footer -->
                <div class="sticky bottom-0 p-6 lg:p-8 border-t border-slate-100 bg-white shadow-[0_-10px_40px_rgba(0,0,0,0.04)]">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex flex-[1] gap-2">
                            <a href="{{ Storage::disk('public')->url($selectedTemplate->file_path) }}" 
                               download
                               class="flex-1 flex items-center justify-center gap-2 py-4 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold rounded-2xl transition-all border border-slate-200"
                               title="Descargar Original">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                            
                            @if($selectedTemplate->tenant_id === auth()->user()->tenant_id)
                            <button wire:click="deleteTemplate({{ $selectedTemplate->id }})" 
                                    wire:confirm="¿Estás seguro de eliminar este formato permanentemente?"
                                    class="flex-1 flex items-center justify-center gap-2 py-4 bg-red-50 hover:bg-red-100 text-red-500 font-bold rounded-2xl transition-all border border-red-100"
                                    title="Eliminar Formato">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endif
                        </div>

                        <button wire:click="personalizeTemplate({{ $selectedTemplate->id }})" 
                                class="flex-[2] bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-8 rounded-2xl shadow-lg shadow-indigo-500/30 transition-all flex items-center justify-center gap-3 group">
                            <span>Personalizar Formato</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    <!-- Personalize Modal -->
    <div x-show="$wire.showPersonalizeModal" 
         class="fixed inset-0 z-[110] overflow-y-auto"
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md" @click="$wire.showPersonalizeModal = false"></div>

            <div class="relative bg-white rounded-[2.5rem] w-full max-w-xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
                @if($selectedTemplate)
                <div class="p-10">
                    <div class="flex justify-between items-start mb-8">
                        <div class="space-y-1">
                            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">Personalizar con IA</h3>
                            <p class="text-slate-500 text-sm">Completa los datos para generar tu documento.</p>
                        </div>
                        <button @click="$wire.showPersonalizeModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="generateDocument" class="space-y-6">
                        @forelse($formPlaceholders as $placeholder => $value)
                            <div class="space-y-2">
                                <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest pl-1">{{ str_replace(['[', ']', '_'], ['', '', ' '], $placeholder) }}</label>
                                <input type="text" 
                                       wire:model="formPlaceholders.{{ $placeholder }}" 
                                       class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium text-slate-700"
                                       placeholder="Escribe el valor para {{ $placeholder }}...">
                            </div>
                        @empty
                            <div class="py-10 text-center bg-indigo-50 rounded-3xl border border-dashed border-indigo-200">
                                <p class="text-indigo-600 font-bold mb-1">Este formato no requiere variables.</p>
                                <p class="text-indigo-400 text-xs text-balance">Puedes descargarlo directamente o generar una copia limpia.</p>
                            </div>
                        @endforelse

                        <div class="pt-6 space-y-4">
                            <button type="submit" 
                                    wire:loading.attr="disabled"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-5 rounded-2xl shadow-xl shadow-indigo-500/30 transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
                                <span wire:loading.remove>Generar Documento Final</span>
                                <span wire:loading>La IA está trabajando...</span>
                                <svg wire:loading.remove class="w-5 h-5 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </button>
                            <p class="text-[10px] text-center text-slate-400 font-medium italic">
                                Al generar, Diogenes creará una copia personalizada en formato {{ $selectedTemplate->extension }} lista para imprimir.
                            </p>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
