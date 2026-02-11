<div class="space-y-6">
    <!-- Header/Search Section -->
    <div class="relative bg-slate-900 rounded-3xl p-8 overflow-hidden shadow-2xl border border-slate-800">
        <!-- Background Accents -->
        <div class="absolute top-0 right-0 -trnaslate-y-1/2 translate-x-1/2 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 trnaslate-y-1/2 -translate-x-1/2 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-2xl mx-auto text-center space-y-4">
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Biblioteca de Formatos</h1>
            <p class="text-slate-400 text-sm">Encuentra formatos legales, contratos y precedentes validados por la IA.</p>
            
            <div class="relative mt-6">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       class="block w-full pl-11 pr-4 py-4 bg-slate-800/50 border border-slate-700 rounded-2xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all backdrop-blur-md"
                       placeholder="Buscar por nombre o concepto jurídico...">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <span class="text-slate-600 text-[10px] font-mono border border-slate-700 px-1.5 py-0.5 rounded">⌘K</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide">
        <button wire:click="selectCategory('Todos')" 
                class="px-5 py-2 rounded-xl text-sm font-bold transition-all {{ $selectedCategory == 'Todos' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100' }}">
            Todos
        </button>
        @foreach($categories as $category)
            <button wire:click="selectCategory('{{ $category }}')" 
                    class="px-5 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ $selectedCategory == $category ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100' }}">
                {{ $category }}
            </button>
        @endforeach
    </div>

    <!-- Main Grid / Preview Layout -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Grid -->
        <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($templates as $template)
                    <div wire:click="selectTemplate({{ $template->id }})" 
                         class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-500/5 transition-all cursor-pointer relative overflow-hidden">
                        
                        <!-- Gradient Accent -->
                        <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500 group-hover:w-1.5 transition-all"></div>

                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors shadow-inner">
                                @if($template->category == 'Contratos')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif($template->category == 'Familiar')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                @endif
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-800 leading-tight group-hover:text-indigo-600 transition-colors">{{ $template->name }}</h3>
                                <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $template->description }}</p>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                <span class="px-2 py-1 rounded-md bg-slate-100 text-[10px] font-bold text-slate-500 uppercase">{{ $template->materia }}</span>
                                <span class="text-[10px] text-slate-400">{{ $template->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <h3 class="text-slate-800 font-bold">No se encontraron formatos</h3>
                        <p class="text-slate-500 text-sm">Intenta ajustar tu búsqueda o filtros.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-8">
                {{ $templates->links() }}
            </div>
        </div>

        <!-- Lateral Preview (Premium Sidebar) -->
        <aside class="w-full lg:w-96 shrink-0 h-fit sticky top-6 {{ $showPreview ? 'block' : 'hidden lg:block' }}">
            @if($selectedTemplate)
                <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col h-[calc(100vh-120px)] max-h-[800px]">
                    <div class="p-6 bg-slate-50 border-b border-slate-200 flex justify-between items-start">
                        <div>
                            <h2 class="font-extrabold text-slate-800 leading-tight">{{ $selectedTemplate->name }}</h2>
                            <p class="text-xs text-indigo-600 font-bold mt-1 uppercase tracking-wider">{{ $selectedTemplate->category }}</p>
                        </div>
                        <button wire:click="closePreview" class="p-2 hover:bg-slate-200 rounded-full transition-colors">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        <!-- Placeholders Detected -->
                        @if(!empty($selectedTemplate->placeholders))
                        <div class="space-y-3">
                            <h3 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Variables Detectadas por IA</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedTemplate->placeholders as $ph)
                                    <span class="px-2 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-md border border-emerald-100">{{ $ph }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Document Preview Content -->
                        <div class="space-y-3">
                            <h3 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Vista Previa de Texto</h3>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-[11px] font-mono text-slate-600 leading-relaxed whitespace-pre-wrap select-none opacity-80 italic">
                                {{ $selectedTemplate->extracted_text }}
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-slate-100 bg-white">
                        <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-emerald-500/30 transition-all flex items-center justify-center gap-2 group">
                            <span>Personalizar y Descargar</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                        <p class="text-[10px] text-center text-slate-400 mt-4 leading-tight italic">Al hacer clic en personalizar, la IA te ayudará a llenar los campos resaltados automáticamente.</p>
                    </div>
                </div>
            @else
                <div class="bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 h-[600px] flex flex-col items-center justify-center text-center p-8 space-y-4">
                    <div class="w-20 h-20 bg-white shadow-xl rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-slate-400 font-bold">Selecciona un formato</h3>
                        <p class="text-slate-400 text-xs">Podrás visualizar el contenido y las variables detectadas antes de descargar.</p>
                    </div>
                </div>
            @endif
        </aside>
    </div>
</div>
