<div class="fixed inset-0 overflow-y-auto" 
     style="z-index: 99999 !important; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;"
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true" 
     x-data>
     
    <style>
        .welcome-video-container {
            width: 100%;
        }
        @media (min-width: 768px) {
            .welcome-video-container {
                width: 70% !important;
                margin: 0 auto;
            }
        }
    </style>

    <!-- Backdrop con estilo inline para asegurar transparencia -->
    <div class="fixed inset-0 transition-opacity backdrop-blur-sm bg-slate-900/40"
         style="background-color: rgba(15, 23, 42, 0.4); position: fixed; top: 0; left: 0; width: 100%; height: 100%;"></div>

    <!-- Contenedor Principal Centrado -->
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0"
         style="position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
         
        <!-- Modal panel -->
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl ring-1 ring-black/5 border border-gray-100"
             style="background-color: white; border-radius: 1rem; max-width: 56rem; width: 100%; margin: 2rem;">
            
            <!-- Close Button -->
            <div class="absolute right-4 top-4 z-10" style="position: absolute; top: 1rem; right: 1rem; z-index: 10;">
                <button type="button" wire:click="closeAndMarkAsSeen" class="rounded-full bg-white/80 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition shadow-sm border border-gray-100 backdrop-blur-sm">
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-8 pt-12 pb-8 sm:px-12 sm:pt-12 sm:pb-8" style="padding: 2rem 3rem;">
                <div class="flex flex-col items-center">
                    
                    <!-- Header Section -->
                    <div class="text-center w-full max-w-3xl mb-8">
                        <h3 class="text-3xl font-serif font-medium tracking-tight text-slate-900 mb-4 leading-tight" id="modal-title" style="font-family: ui-serif, Georgia, Cambria, 'Times New Roman', Times, serif; font-size: 1.875rem; color: #0f172a;">
                            {{ $title }}
                        </h3>
                        <div class="w-16 h-1 bg-indigo-600 mx-auto rounded-full mb-6 opacity-80" style="height: 0.25rem; width: 4rem; background-color: #4f46e5; margin: 0 auto 1.5rem auto; border-radius: 9999px;"></div>
                        <p class="text-lg text-gray-600 leading-relaxed font-light">
                            {{ $message }}
                        </p>
                    </div>

                    <!-- Video Container (Responsive) -->
                    <div class="welcome-video-container aspect-video bg-gray-50 rounded-lg overflow-hidden shadow-2xl ring-1 ring-black/5 relative group mb-2" 
                         style="aspect-ratio: 16/9; background-color: #f9fafb; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
                        @if($videoType === 'youtube')
                            <iframe 
                                class="w-full h-full" 
                                style="width: 100%; height: 100%;"
                                src="{{ $embedUrl }}" 
                                title="Welcome Video" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen>
                            </iframe>
                        @elseif($videoType === 'mp4')
                            <video class="w-full h-full" style="width: 100%; height: 100%;" controls autoplay>
                                <source src="{{ $embedUrl }}" type="video/mp4">
                                Tu navegador no soporta el elemento de video.
                            </video>
                        @else
                            <div class="flex flex-col items-center justify-center h-full text-gray-400 bg-gray-50" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #9ca3af;">
                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="font-medium">Video de bienvenida no configurado</p>
                            </div>
                        @endif
                    </div>
                    
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-white px-8 py-5 sm:flex sm:flex-row sm:justify-end sm:px-12 border-t border-gray-100" style="background-color: white; padding: 1.25rem 2rem; border-top: 1px solid #f3f4f6; display: flex; justify-content: flex-end;">
                <button type="button" wire:click="closeAndMarkAsSeen" class="inline-flex w-full justify-center items-center rounded-lg bg-slate-900 border border-transparent px-8 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all sm:w-auto tracking-wide"
                        style="background-color: #0f172a; color: white; padding: 0.625rem 2rem; border-radius: 0.5rem; font-weight: 500; display: inline-flex; align-items: center;">
                    <span>Comenzar</span>
                    <svg class="ml-2 -mr-1 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>
</div>
