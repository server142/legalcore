<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Crear Nuevo Expediente') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Número de Expediente -->
                <div>
                    <x-input-label for="numero" :value="__('Número de Expediente')" />
                    <x-text-input id="numero" type="text" class="mt-1 block w-full" wire:model="numero" placeholder="Ej. 123/2024" />
                    <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                </div>

                <!-- Título -->
                <div>
                    <x-input-label for="titulo" :value="__('Título / Carátula')" />
                    <x-text-input id="titulo" type="text" class="mt-1 block w-full" wire:model="titulo" placeholder="Ej. Perez vs Garcia" />
                    <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                </div>

                <!-- Materia -->
                <div>
                    <div class="flex justify-between items-center">
                        <x-input-label for="materia" :value="__('Materia')" />
                        <button type="button" wire:click="$set('showMateriaModal', true)" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">+ Nueva Materia</button>
                    </div>
                    <select id="materia" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model="materia">
                        <option value="">Seleccione una materia</option>
                        @foreach($materias as $m)
                            <option value="{{ $m->nombre }}">{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('materia')" class="mt-2" />
                </div>

                <!-- Juzgado -->
                <div>
                    <x-input-label for="juzgado" :value="__('Juzgado / Tribunal')" />
                    <x-text-input id="juzgado" type="text" class="mt-1 block w-full" wire:model="juzgado" placeholder="Ej. Juzgado 5to de lo Civil" />
                    <x-input-error :messages="$errors->get('juzgado')" class="mt-2" />
                </div>

                <!-- Nombre del Juez -->
                <div>
                    <x-input-label for="nombre_juez" :value="__('Nombre del Juez')" />
                    <x-text-input id="nombre_juez" type="text" class="mt-1 block w-full" wire:model="nombre_juez" placeholder="Ej. Lic. Juan Carlos Lopez" />
                    <x-input-error :messages="$errors->get('nombre_juez')" class="mt-2" />
                </div>

                <!-- Cliente -->
                <div>
                    <div class="flex justify-between items-center">
                        <x-input-label for="cliente_id" :value="__('Cliente')" />
                        <button type="button" wire:click="$set('showClienteModal', true)" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">+ Nuevo Cliente</button>
                    </div>
                    <select id="cliente_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model="cliente_id">
                        <option value="">Seleccione un cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                </div>

                <!-- Abogado Responsable -->
                <div>
                    <div class="flex justify-between items-center">
                        <x-input-label for="abogado_responsable_id" :value="__('Abogado Responsable')" />
                        <button type="button" wire:click="$set('showAbogadoModal', true)" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">+ Nuevo Abogado</button>
                    </div>
                    <select id="abogado_responsable_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model="abogado_responsable_id">
                        <option value="">Seleccione un abogado</option>
                        @foreach($abogados as $abogado)
                            <option value="{{ $abogado->id }}">{{ $abogado->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('abogado_responsable_id')" class="mt-2" />
                </div>

                <!-- Fecha de Inicio -->
                <div>
                    <x-input-label for="fecha_inicio" :value="__('Fecha de Inicio')" />
                    <x-text-input id="fecha_inicio" type="date" class="mt-1 block w-full" wire:model="fecha_inicio" />
                    <x-input-error :messages="$errors->get('fecha_inicio')" class="mt-2" />
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <x-input-label for="descripcion" :value="__('Descripción / Notas Iniciales')" />
                    <textarea id="descripcion" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4" wire:model="descripcion"></textarea>
                    <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                </div>

                <div class="md:col-span-2 flex justify-end space-x-3">
                    <a href="{{ route('expedientes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancelar</a>
                    <x-primary-button>
                        {{ __('Crear Expediente') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modals -->
    
    <!-- Modal Materia -->
    @if($showMateriaModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showMateriaModal', false)"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-50">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Nueva Materia</h3>
                    <x-input-label for="newMateriaNombre" :value="__('Nombre de la Materia')" />
                    <x-text-input id="newMateriaNombre" type="text" class="mt-1 block w-full" wire:model="newMateriaNombre" placeholder="Ej. Familiar" />
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="$set('showMateriaModal', false)" class="text-sm text-gray-500">Cancelar</button>
                        <x-primary-button wire:click="createMateria">Guardar</x-primary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Cliente -->
    @if($showClienteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showClienteModal', false)"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-50">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Nuevo Cliente</h3>
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="newClienteNombre" :value="__('Nombre Completo')" />
                            <x-text-input id="newClienteNombre" type="text" class="mt-1 block w-full" wire:model="newClienteNombre" />
                        </div>
                        <div>
                            <x-input-label for="newClienteEmail" :value="__('Email')" />
                            <x-text-input id="newClienteEmail" type="email" class="mt-1 block w-full" wire:model="newClienteEmail" />
                        </div>
                        <div>
                            <x-input-label for="newClienteTelefono" :value="__('Teléfono')" />
                            <x-text-input id="newClienteTelefono" type="text" class="mt-1 block w-full" wire:model="newClienteTelefono" />
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="$set('showClienteModal', false)" class="text-sm text-gray-500">Cancelar</button>
                        <x-primary-button wire:click="createCliente">Guardar Cliente</x-primary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Abogado -->
    @if($showAbogadoModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showAbogadoModal', false)"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-50">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Nuevo Abogado</h3>
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="newAbogadoNombre" :value="__('Nombre Completo')" />
                            <x-text-input id="newAbogadoNombre" type="text" class="mt-1 block w-full" wire:model="newAbogadoNombre" />
                        </div>
                        <div>
                            <x-input-label for="newAbogadoEmail" :value="__('Email')" />
                            <x-text-input id="newAbogadoEmail" type="email" class="mt-1 block w-full" wire:model="newAbogadoEmail" />
                        </div>
                        <p class="text-xs text-gray-500 italic">* La contraseña por defecto será: password123</p>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="$set('showAbogadoModal', false)" class="text-sm text-gray-500">Cancelar</button>
                        <x-primary-button wire:click="createAbogado">Guardar Abogado</x-primary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
