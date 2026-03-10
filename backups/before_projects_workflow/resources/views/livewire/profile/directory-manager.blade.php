<div class="px-4 py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        
        <!-- Preview Section (Sticky) -->
        <div class="md:col-span-1 order-last md:order-first mb-8 md:mb-0">
            <div class="sticky top-6">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Vista Previa en Directorio</h3>
                
                <!-- Lawyer Card Preview -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200 hover:shadow-2xl transition-shadow duration-300 relative group">
                    @php $dirPhoto = $user->directoryProfile?->profile_photo_url; @endphp
                    @if($user->directoryProfile?->profile_photo_path)
                        <div class="h-32 bg-indigo-600 bg-cover bg-center" style="background-image: url('{{ $dirPhoto }}'); filter: blur(20px); opacity: 0.3;"></div>
                        <div class="absolute top-4 right-4">
                            @if($is_public)
                                <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full border border-green-200 flex items-center shadow-sm">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                    Público
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2 py-1 rounded-full border border-gray-200 flex items-center shadow-sm opacity-70">
                                    Oculto
                                </span>
                            @endif
                        </div>
                        <img class="h-24 w-24 rounded-full border-4 border-white shadow-md mx-auto -mt-12 object-cover relative z-10" src="{{ $dirPhoto }}" alt="{{ $user->name }}">
                    @else
                        <div class="h-32 bg-gray-200"></div>
                        <div class="h-24 w-24 rounded-full bg-gray-300 border-4 border-white shadow-md mx-auto -mt-12 flex items-center justify-center text-3xl font-bold text-gray-500">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    
                    <div class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center gap-1 mb-1">
                            <h2 class="text-xl font-bold text-gray-900 truncate">{{ $user->name }}</h2>
                            @if($professional_license)
                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20" title="Cédula Verificada"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            @endif
                        </div>
                        
                        <p class="text-sm font-medium text-indigo-600 mb-2">{{ $headline ?: 'Abogado' }}</p>
                        
                        @if($city && $state)
                            <p class="text-xs text-gray-500 flex justify-center items-center mb-4">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $city }}, {{ $state }}
                            </p>
                        @endif

                        <div class="flex flex-wrap justify-center gap-1 mb-4">
                            @forelse($specialties as $tag)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ $tag }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 italic">Sin especialidades agregadas</span>
                            @endforelse
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-2">
                            <button class="w-full bg-indigo-600 text-white text-xs font-bold py-2 rounded-lg shadow-sm opacity-90 cursor-default">
                                Agendar Cita
                            </button>
                            <button class="w-full bg-white text-gray-700 border border-gray-300 text-xs font-bold py-2 rounded-lg shadow-sm opacity-90 cursor-default">
                                Ver Perfil
                            </button>
                        </div>
                    </div>
                    
                    @if(!$headline)
                        <div class="absolute inset-0 bg-white/80 backdrop-blur-[1px] flex items-center justify-center rounded-xl z-20">
                           <div class="text-center p-4">
                               <p class="text-sm font-bold text-gray-500">Completa tu perfil para ver la vista previa real</p>
                           </div>
                        </div>
                    @endif
                </div>

                <div class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-4">
                    <h4 class="text-xs font-bold text-blue-800 uppercase mb-2">💡 Tip de Posicionamiento</h4>
                    <p class="text-xs text-blue-700 leading-relaxed">
                        Los perfiles con foto profesional y al menos 3 especialidades confirmadas reciben <strong>4.5x más clics</strong> en el directorio. Asegúrate de que tu "Headline" sea claro y directo (ej. "Especialista en Pensiones Alimenticias").
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="md:col-span-2">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <div class="mb-6 border-b pb-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Tu Perfil Público</h3>
                            <p class="mt-1 text-sm text-gray-500">Esta información será visible para miles de clientes potenciales en nuestro directorio.</p>
                        </div>
                        
                        <!-- Toggle Public -->
                        <div class="flex items-center">
                            <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                                <input type="checkbox" wire:model.live="is_public" id="toggle-public" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer {{ $is_public ? 'right-0 border-green-400' : 'left-0 border-gray-300' }}"/>
                                <label for="toggle-public" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer {{ $is_public ? 'bg-green-400' : '' }}"></label>
                            </div>
                                {{ $is_public ? 'Perfil Visible' : 'Perfil Oculto' }}
                            </label>
                            
                             <style>
                                .toggle-checkbox:checked {
                                    right: 0;
                                    border-color: #68D391;
                                }
                                .toggle-checkbox:checked + .toggle-label {
                                    background-color: #68D391;
                                }
                            </style>
                        </div>
                    </div>
                    
                    <!-- Links de Ayuda y Visibilidad -->
                    <div class="mb-6 flex space-x-4 text-xs">
                        <a href="{{ route('directory.public') }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Ver Directorio Público
                        </a>
                        <a href="{{ route('manual.index') }}?page=guia-directorio-publico" target="_blank" class="text-gray-500 hover:text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            ¿Cómo funciona? (Ver Manual)
                        </a>
                    </div>
                    
                    <!-- Foto de Perfil Pública -->
                    <div class="mb-6 grid grid-cols-6 gap-6" x-data="{photoName: null, photoPreview: null}">
                        <div class="col-span-6 sm:col-span-4" 
                             x-data="{
                                photoName: null,
                                photoPreview: null,
                                cropModalOpen: false,
                                cropper: null,
                                uploading: false,
                                initCropper() {
                                    if (this.cropper) {
                                        this.cropper.destroy();
                                        this.cropper = null;
                                    }
                                    const image = document.getElementById('image-to-crop');
                                    this.cropper = new Cropper(image, {
                                        aspectRatio: 1,
                                        viewMode: 1,
                                        autoCropArea: 0.8,
                                        background: false,
                                        minCropBoxWidth: 100,
                                        minCropBoxHeight: 100,
                                        ready: () => {
                                            // Cropper listo, habilitar botón
                                        }
                                    });
                                },
                                selectPhoto() {
                                    $refs.photoInput.click();
                                },
                                fileChosen(event) {
                                    const file = event.target.files[0];
                                    if (!file) return;

                                    this.photoName = file.name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        const img = document.getElementById('image-to-crop');
                                        
                                        // Destruir cropper anterior si existe
                                        if (this.cropper) {
                                            this.cropper.destroy();
                                            this.cropper = null;
                                        }
                                        
                                        // Resetear la imagen completamente
                                        img.src = '';
                                        
                                        // Abrir modal primero (para que el elemento sea visible)
                                        this.cropModalOpen = true;
                                        
                                        // Esperar al siguiente ciclo del DOM para que el modal sea visible
                                        this.$nextTick(() => {
                                            img.onload = () => {
                                                // La imagen cargó su data URL: ahora inicializar cropper
                                                this.initCropper();
                                            };
                                            img.src = e.target.result;
                                        });
                                    };
                                    reader.readAsDataURL(file);
                                },
                                cancelCrop() {
                                    this.cropModalOpen = false;
                                     if (this.cropper) {
                                        this.cropper.destroy();
                                        this.cropper = null;
                                    }
                                    $refs.photoInput.value = ''; // Reset input
                                },
                                saveCrop() {
                                    if (!this.cropper) return;
                                    
                                    this.uploading = true;
                                    this.cropper.getCroppedCanvas({
                                        width: 400,
                                        height: 400,
                                        fillColor: '#fff'
                                    }).toBlob((blob) => {
                                        // Convertir Blob a File con nombre para que Livewire pueda leer metadata
                                        const file = new File([blob], 'profile-photo.jpg', { type: 'image/jpeg' });
                                        
                                        // 1. Subir a Livewire como TemporaryUploadedFile
                                        @this.upload('photo', file, (uploadedFilename) => {
                                            // 2. Una vez subida, llamar savePhoto() que persiste a DB
                                            @this.call('savePhoto').then(() => {
                                                this.uploading = false;
                                                this.cropModalOpen = false;
                                                if (this.cropper) {
                                                    this.cropper.destroy();
                                                    this.cropper = null;
                                                }
                                                
                                                // Limpiar preview local: Livewire re-renderiza con la foto real del servidor
                                                this.photoPreview = null;
                                            });
                                        }, () => {
                                            // Error callback
                                            this.uploading = false;
                                            alert('Error al subir la imagen. Intenta de nuevo.');
                                        });
                                    }, 'image/jpeg', 0.9);
                                }
                             }"
                        >
                            <!-- Hidden File Input -->
                            <input type="file" x-ref="photoInput" class="hidden" accept="image/*" @change="fileChosen">

                            <label class="block text-sm font-medium text-gray-700">Foto del Directorio</label>
                            
                            <!-- Current Photo Display -->
                            <div class="mt-2 text-center sm:text-left">
                                <div class="relative inline-block" x-show="!photoPreview">
                                    <img src="{{ $user->directoryProfile->profile_photo_url ?? $user->profile_photo_url }}" 
                                         alt="{{ $user->name }}" 
                                         class="rounded-full h-24 w-24 object-cover border-4 border-white shadow-md">
                                </div>
                                
                                <div class="relative inline-block" x-show="photoPreview" style="display: none;">
                                    <img :src="photoPreview" class="rounded-full h-24 w-24 object-cover border-4 border-indigo-100 shadow-md">
                                    <span class="absolute bottom-0 right-0 bg-indigo-500 text-white p-1 rounded-full text-xs shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button type="button" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" @click="selectPhoto">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Subir Nueva Foto
                                </button>

                                @if ($user->directoryProfile?->profile_photo_path)
                                    <button type="button" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-red-600 uppercase tracking-widest shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" wire:click="deleteProfilePhoto">
                                        Eliminar
                                    </button>
                                @endif
                                
                                <div wire:loading wire:target="photo" class="text-xs text-indigo-500 flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Procesando...
                                </div>
                            </div>
                            
                            @error('photo') <span class="text-red-500 text-xs block mt-2">{{ $message }}</span> @enderror

                            <!-- Modal Cropper -->
                            <div x-show="cropModalOpen" style="display: none;" 
                                 class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    
                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Ajustar Foto de Perfil</h3>
                                                    <div class="mt-4">
                                                        <div class="w-full h-96 bg-gray-100 rounded-lg overflow-hidden relative">
                                                            <img id="image-to-crop" src="" alt="Imagen para recortar" class="max-w-full block">
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-2">Arrastra para ajustar el encuadre. Se recortará en formato cuadrado.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50" 
                                                    @click="saveCrop" :disabled="uploading">
                                                <span x-show="!uploading">Guardar y Usar</span>
                                                <span x-show="uploading">Procesando...</span>
                                            </button>
                                            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" 
                                                    @click="cancelCrop" :disabled="uploading">
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="grid grid-cols-6 gap-6">
                        <!-- Headline -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="headline" class="block text-sm font-medium text-gray-700">Titular Profesional</label>
                            <input type="text" wire:model.live.debounce.500ms="headline" id="headline" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Ej. Abogado Penalista | Especialista en Litigio Estratégico">
                            <p class="mt-2 text-xs text-gray-500">Este texto aparece justo debajo de tu nombre. Sé específico.</p>
                            @error('headline') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Cédula -->
                        <div class="col-span-6 sm:col-span-2">
                            <label for="professional_license" class="block text-sm font-medium text-gray-700">Cédula Profesional</label>
                            <input type="text" wire:model.live="professional_license" id="professional_license" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('professional_license') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Bio -->
                        <div class="col-span-6">
                            <label for="bio" class="block text-sm font-medium text-gray-700">Biografía / Presentación</label>
                            <div class="mt-1">
                                <textarea wire:model.live.debounce.500ms="bio" id="bio" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Cuéntale a tus clientes por qué eres la mejor opción para resolver su caso..."></textarea>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Breve descripción de tu experiencia y enfoque.</p>
                            @error('bio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Location -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                            <input type="text" wire:model.live="city" id="city" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="state" class="block text-sm font-medium text-gray-700">Estado / Provincia</label>
                            <input type="text" wire:model.live="state" id="state" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('state') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Specialties (Tags) -->
                        <div class="col-span-6">
                            <label for="specialties" class="block text-sm font-medium text-gray-700">Especialidades (Máx. 10)</label>
                            <div class="mt-2 flex rounded-md shadow-sm">
                                <input type="text" wire:model="newSpecialty" wire:keydown.enter.prevent="addSpecialty" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300" placeholder="Ej. Divorcios, Amparos, Mercantil... (Presiona Enter)">
                                <button type="button" wire:click="addSpecialty" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <span>Agregar</span>
                                </button>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($specialties as $index => $tag)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $tag }}
                                        <button type="button" wire:click="removeSpecialty({{ $index }})" class="flex-shrink-0 ml-1.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none focus:bg-indigo-500 focus:text-white">
                                            <span class="sr-only">Remove specialty</span>
                                            <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8"><path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" /></svg>
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                            @error('specialties') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Social Links -->
                        <div class="col-span-6 border-t pt-4 mt-2">
                            <h4 class="text-sm font-medium text-gray-900 mb-4">Enlaces de Contacto Directo</h4>
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp (Solo números)</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        </span>
                                        <input type="text" wire:model="whatsapp" id="whatsapp" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300" placeholder="Ej. 5512345678">
                                    </div>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="linkedin" class="block text-sm font-medium text-gray-700">LinkedIn URL</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                            in
                                        </span>
                                        <input type="text" wire:model="linkedin" id="linkedin" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300" placeholder="linkedin.com/in/usuario">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 flex justify-between items-center">
                    <div>
                         @if($showSavedMessage)
                            <span class="text-sm text-green-600 font-bold transition-opacity duration-1000 ease-out" x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show">
                                ✓ Cambios guardados correctamente
                            </span>
                        @endif
                    </div>
                    <button type="submit" wire:click="save" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Guardar Perfil
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
