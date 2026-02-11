<div class="space-y-6">
    <!-- Header Section -->
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

    <!-- Search Hero Section -->
    <div class="relative bg-gradient-to-br from-indigo-900 to-slate-900 rounded-[2rem] p-8 md:p-12 overflow-hidden shadow-xl border border-white/10">
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
                    </button>
                    @foreach($categories as $category)
                        <button wire:click="selectCategory('{{ $category }}')" 
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all {{ $selectedCategory == $category ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100' }}">
                            <span>{{ $category }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- Templates Grid -->
        <div class="flex-1 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">
                @forelse($templates as $template)
                    <div wire:click="selectTemplate({{ $template->id }})" 
                         class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-indigo-300 hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer relative overflow-hidden flex flex-col justify-between h-48">
                        
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500 group-hover:w-2 transition-all"></div>

                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <div class="w-10 h-10 rounded-lg bg-slate-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 uppercase tracking-widest">{{ $template->materia }}</span>
                            </div>

                            <div>
                                <h3 class="font-extrabold text-slate-800 leading-tight group-hover:text-indigo-600 transition-colors">{{ $template->name }}</h3>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2 max-w-xs">{{ $template->description }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $template->is_global ? 'bg-emerald-400' : 'bg-indigo-400' }}"></span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase">{{ $template->is_global ? 'Global' : 'Privado' }}</span>
                            </div>
                            <span class="text-[10px] text-slate-300 font-medium">{{ $template->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                        <h3 class="text-slate-800 font-bold">Sin resultados</h3>
                    </div>
                @endforelse
            </div>
            <div class="mt-8">
                {{ $templates->links() }}
            </div>
        </div>
    </div>

    <!-- Modals Container (Single source of truth for visibility) -->
    <div class="modals-container">
        
        <!-- 1. Upload Modal -->
        <div x-show="$wire.activePanel === 'upload'" 
             class="fixed inset-0 z-[120] overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="$wire.activePanel = null"></div>
                <div class="relative bg-white rounded-[2rem] w-full max-w-lg overflow-hidden shadow-2xl">
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Subir Nuevo Formato</h3>
                                <p class="text-slate-500 text-sm mt-1">Usa <span class="text-indigo-600 font-bold">[CORCHETES]</span> para variables.</p>
                            </div>
                            <button @click="$wire.activePanel = null" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <form wire:submit.prevent="saveTemplate" class="space-y-5">
                            <input type="text" wire:model="newTemplateName" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl" placeholder="Nombre...">
                            <div class="grid grid-cols-2 gap-4">
                                <select wire:model="newTemplateCategory" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl">
                                    <option value="">Categoría...</option>
                                    <option value="Contratos">Contratos</option>
                                    <option value="Corporativo">Corporativo</option>
                                    <option value="Familiar">Familiar</option>
                                    <option value="Laboral">Laboral</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                <input type="text" wire:model="newTemplateMateria" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl" placeholder="Materia...">
                            </div>
                            <input type="file" wire:model="newTemplateFile" class="w-full">
                            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Preview Panel (Drawer) -->
        <div x-show="$wire.activePanel === 'preview'" 
             class="fixed inset-0 z-[100] flex justify-end" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="$wire.activePanel = null"></div>
            <div class="relative w-full max-w-2xl bg-white h-full shadow-2xl flex flex-col"
                 x-transition:enter="transition-transform ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0">
                @if($selectedTemplate && $activePanel === 'preview')
                <div class="flex flex-col h-full">
                    <div class="p-8 border-b flex justify-between items-center">
                        <h2 class="text-xl font-bold">{{ $selectedTemplate->name }}</h2>
                        <button @click="$wire.activePanel = null"><svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-8 space-y-6">
                        <div class="p-6 bg-slate-950 rounded-2xl text-[12px] font-mono text-indigo-200/80 leading-relaxed whitespace-pre-wrap min-h-[400px]">
                            {{ $selectedTemplate->extracted_text ?: 'Sin contenido extraído.' }}
                        </div>
                    </div>
                    <div class="p-8 border-t flex gap-4">
                        <button wire:click="deleteTemplate({{ $selectedTemplate->id }})" class="p-4 bg-red-50 text-red-500 rounded-2xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        <button wire:click="personalizeTemplate({{ $selectedTemplate->id }})" class="flex-1 bg-indigo-600 text-white font-bold rounded-2xl">Personalizar Formato</button>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- 3. Personalize Modal -->
        <div x-show="$wire.activePanel === 'personalize'" 
             class="fixed inset-0 z-[130] overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md" @click="$wire.activePanel = null"></div>
                <div class="relative bg-white rounded-[2.5rem] w-full max-w-xl shadow-2xl p-10">
                    @if($selectedTemplate && $activePanel === 'personalize')
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h3 class="text-2xl font-bold">Personalizar</h3>
                            <p class="text-slate-500 text-sm">Llena los campos para generar el Word.</p>
                        </div>
                        <button @click="$wire.activePanel = null"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form wire:submit.prevent="generateDocument" class="space-y-6">
                        @foreach($formPlaceholders as $ph => $val)
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ str_replace(['[', ']', '_'], '', $ph) }}</label>
                                <input type="text" wire:model="formPlaceholders.{{ $ph }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl" placeholder="Escribe aquí...">
                            </div>
                        @endforeach
                        <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-lg">Generar Word Final</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
