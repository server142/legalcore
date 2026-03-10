<div x-data="{ 
    texto: @entangle('texto'),
    initQuill() {
        const quill = new Quill($refs.editor, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'align': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'clean']
                ]
            }
        });
        quill.root.innerHTML = this.texto;
        quill.on('text-change', () => {
            this.texto = quill.root.innerHTML;
        });
    }
}" x-init="initQuill()">
    <x-slot name="header">
        <x-header title="{{ $legalDocumentId ? 'Editar Documento' : 'Nuevo Documento Legal' }}" backUrl="{{ route('admin.legal-documents.index') }}" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Columna Izquierda: Configuración -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b pb-2">Configuración</h3>
                        
                        <div>
                            <x-input-label for="nombre" value="Nombre del Documento" />
                            <x-text-input wire:model="nombre" id="nombre" type="text" class="mt-1 block w-full text-sm" placeholder="Ej: Aviso de Privacidad" />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tipo" value="Categoría / Tipo" />
                            <select wire:model="tipo" id="tipo" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="PRIVACIDAD">Aviso de Privacidad</option>
                                <option value="TERMINOS">Términos y Condiciones</option>
                                @if(auth()->user()->hasRole('super_admin'))
                                    <option value="COOKIES">Política de Cookies</option>
                                    <option value="CONTRATO_SAAS">Contrato SaaS</option>
                                @endif
                                <option value="CONTRATO_SERVICIOS">Contrato de Prestación de Servicios (Plantilla)</option>
                                <option value="OTRO">Otro</option>
                            </select>
                            <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="version" value="Versión" />
                            <x-text-input wire:model="version" id="version" type="text" class="mt-1 block w-full text-sm font-mono" placeholder="1.0" />
                            <p class="text-[10px] text-gray-400 mt-1 italic">Si cambias la versión, se solicitará nueva aceptación a los usuarios.</p>
                            <x-input-error :messages="$errors->get('version')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="fecha_publicacion" value="Fecha de Publicación" />
                            <x-text-input wire:model="fecha_publicacion" id="fecha_publicacion" type="datetime-local" class="mt-1 block w-full text-sm" />
                            <x-input-error :messages="$errors->get('fecha_publicacion')" class="mt-2" />
                        </div>

                        <div class="space-y-3 pt-2">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="activo" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Documento Activo</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" wire:model="requiere_aceptacion" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Requiere Aceptación Obligatoria</span>
                            </label>
                        </div>
                    </div>

                    @if(auth()->user()->hasRole('super_admin'))
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b pb-2">Visibilidad</h3>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach(['registro', 'login', 'onboarding', 'footer'] as $pos)
                                <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                                    <input type="checkbox" wire:model="visible_en" value="{{ $pos }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-xs font-bold text-gray-600 uppercase">{{ $pos }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Columna Derecha: Editor de Texto -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col h-full min-h-[500px]">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest border-b pb-2 mb-4">Contenido Legal</h3>
                        
                        <div class="flex-1 flex flex-col">
                            <div wire:ignore class="flex-1">
                                <div x-ref="editor" class="h-96"></div>
                            </div>
                            <x-input-error :messages="$errors->get('texto')" class="mt-2" />
                        </div>

                        <div class="mt-6 flex justify-end space-x-3 relative z-50">
                            <a href="{{ route('admin.legal-documents.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-bold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ $legalDocumentId ? 'Guardar Cambios' : 'Crear Documento' }}
                            </x-primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <style>
            .ql-editor {
                min-height: 300px;
                font-family: 'Figtree', sans-serif;
                font-size: 14px;
            }
            .ql-toolbar.ql-snow {
                border-top-left-radius: 0.5rem;
                border-top-right-radius: 0.5rem;
                background-color: #f9fafb;
            }
            .ql-container.ql-snow {
                border-bottom-left-radius: 0.5rem;
                border-bottom-right-radius: 0.5rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    @endpush
</div>
