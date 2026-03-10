<div class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="md:flex md:items-center md:justify-between mb-10">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-black text-gray-900 sm:truncate tracking-tight flex items-center gap-3">
                    <div class="p-2 bg-indigo-600 rounded-xl shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    Proyectos Inteligentes
                </h2>
                <p class="mt-2 text-sm text-gray-500 font-medium">Automatiza el inicio de tus casos con flujos de trabajo guiados.</p>
            </div>
            <div class="mt-4 flex md:ml-4 md:mt-0">
                <!-- Search -->
                <div class="relative rounded-full shadow-sm max-w-xs">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" wire:model.live="search" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-200 rounded-full py-2" placeholder="Buscar proyecto...">
                </div>
            </div>
        </div>

        <!-- Workflow Grid (Premium Selection) -->
        <div class="mb-12">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 uppercase tracking-widest text-[11px]">
                <span class="w-8 h-[2px] bg-indigo-500"></span>
                Iniciar Nuevo Proceso
            </h3>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($workflows as $wf)
                <a href="{{ route('projects.wizard', ['workflow' => $wf->id]) }}" 
                   class="group relative bg-white border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 cursor-pointer overflow-hidden">
                    
                    <!-- Decorative Gradient Background -->
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                    
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-{{ $wf->materia == 'Familiar' ? 'pink' : 'indigo' }}-100 rounded-2xl text-{{ $wf->materia == 'Familiar' ? 'pink' : 'indigo' }}-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                <!-- Dynamic Icon (fallback to generic) -->
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800 uppercase tracking-tighter">
                                {{ $wf->materia }}
                            </span>
                        </div>
                        <h4 class="text-xl font-black text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">{{ $wf->name }}</h4>
                        <p class="text-sm text-gray-500 leading-relaxed mb-6">{{ $wf->description }}</p>
                        
                        <div class="flex items-center text-sm font-bold text-indigo-600">
                            Comenzar ahora
                            <svg class="ml-2 w-4 h-4 transform group-hover:translate-x-2 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                    </div>
                </a>
                @endforeach

                <!-- Placeholder for many more -->
                <button wire:click="requestMoreFlows" class="group bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl p-6 flex flex-col items-center justify-center text-center hover:bg-white hover:border-indigo-300 transition-all">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <p class="text-sm font-bold text-gray-400 group-hover:text-indigo-600">Solicitar más flujos</p>
                </button>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-black text-gray-900">Proyectos en Curso</h3>
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">{{ $projects->total() }} Activos</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-8 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Proyecto</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Flujo</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Estado / Progreso</th>
                            <th class="px-8 py-4 text-right text-[10px] font-black text-gray-500 uppercase tracking-widest">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($projects as $project)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-cyan-500 rounded-xl flex items-center justify-center text-white font-bold">
                                        {{ substr($project->title, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $project->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $project->cliente ? $project->cliente->nombre : 'Sin Cliente' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-medium rounded-lg border border-gray-200 text-gray-600">
                                    {{ $project->workflow->name }}
                                </span>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="w-full max-w-xs">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-bold text-indigo-600 uppercase">{{ $project->status }}</span>
                                        <span class="text-[10px] font-bold text-gray-600">{{ $project->progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $project->progress }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center p-2 border border-transparent rounded-full text-indigo-600 hover:bg-indigo-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-gray-400 italic">
                                No tienes proyectos iniciados. ¡Comienza uno arriba!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($projects->hasPages())
            <div class="px-8 py-4 border-t border-gray-100">
                {{ $projects->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
