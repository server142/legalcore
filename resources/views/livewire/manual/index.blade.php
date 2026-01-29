<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Centro de Ayuda Diogenes') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Guía completa para el uso eficiente de su despacho jurídico.</p>
            </div>
            @if(auth()->user()->hasRole('super_admin'))
                <a href="{{ route('manual.manage') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    {{ __('Gestionar Contenido') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Navigation -->
                <div class="lg:w-1/4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6 lg:sticky lg:top-8 h-[calc(100vh-4rem)] overflow-y-auto">
                        <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-6">Índice de Temas</h3>
                        <nav class="space-y-2 pr-2">
                            @foreach($pages as $page)
                                <a href="#{{ $page->slug }}" 
                                   class="group flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50/50 rounded-xl transition-all duration-200 border border-transparent hover:border-indigo-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-indigo-500 mr-3 transition-colors"></span>
                                    {{ $page->title }}
                                </a>
                            @endforeach
                        </nav>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="lg:w-3/4 space-y-8">
                    @forelse($pages as $page)
                        <article id="{{ $page->slug }}" class="bg-white rounded-3xl shadow-sm border border-gray-200/60 overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <!-- Header del Post -->
                            <div class="p-8 border-b border-gray-50">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                                        <span class="text-lg font-bold">{{ $loop->iteration }}</span>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $page->title }}</h2>
                                        <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">Módulo del Sistema</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Imagen Destacada -->
                            @if($page->image_path)
                                <div class="px-8 pt-4">
                                    <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-inner bg-gray-50">
                                        <img src="{{ asset('storage/' . $page->image_path) }}" 
                                             alt="{{ $page->title }}" 
                                             class="w-full h-auto transform hover:scale-[1.02] transition-transform duration-500">
                                    </div>
                                </div>
                            @endif

                            <!-- Cuerpo del Contenido -->
                            <div class="p-8">
                                <div class="prose prose-indigo max-w-none">
                                    <div class="text-gray-600 leading-relaxed text-lg space-y-4">
                                        {!! Str::markdown($page->content) !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Footer del Post -->
                            <div class="px-8 py-4 bg-gray-50/50 border-t border-gray-50 flex justify-between items-center">
                                <span class="text-xs text-gray-400">Diogenes v1.0 • Manual de Usuario</span>
                                <a href="#header" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Volver arriba ↑</a>
                            </div>
                        </article>
                    @empty
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-200/60 p-16 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Manual en preparación</h3>
                            <p class="text-gray-500">Estamos actualizando el contenido para brindarle la mejor experiencia.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos personalizados para el contenido Markdown */
        .prose strong {
            color: #4f46e5;
            font-weight: 700;
        }
        .prose ul {
            list-style-type: none;
            padding-left: 0;
        }
        .prose li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .prose li::before {
            content: "→";
            position: absolute;
            left: 0;
            color: #6366f1;
            font-weight: bold;
        }
        html {
            scroll-behavior: smooth;
        }
    </style>
</div>
