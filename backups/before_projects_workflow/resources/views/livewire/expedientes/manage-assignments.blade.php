<x-slot name="header">
    <x-header title="Asignaciones: {{ $expediente->numero }}" subtitle="Control de accesos y responsable del caso" />
</x-slot>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Abogado Responsable</h3>
        
        <div class="space-y-4">
            <div>
                <x-input-label for="newResponsible" :value="__('Abogado Responsable Principal')" />
                <select wire:model="newResponsible" id="newResponsible" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    @foreach($abogados as $abogado)
                        <option value="{{ $abogado->id }}">{{ $abogado->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Este abogado será el responsable principal del expediente</p>
            </div>

            <div class="flex justify-end items-center">
                <x-action-message class="mr-3" on="responsible-changed">
                    {{ __('Guardado.') }}
                </x-action-message>
                <x-primary-button wire:click="changeResponsible">
                    {{ __('Cambiar Responsable') }}
                </x-primary-button>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Abogados Asignados</h3>
        <p class="text-sm text-gray-600 mb-4">Seleccione todos los abogados que tendrán acceso a este expediente</p>
        
        <div class="space-y-2">
            @foreach($abogados as $abogado)
                <label class="flex items-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition">
                    <input type="checkbox" 
                           wire:model="selectedUsers" 
                           value="{{ $abogado->id }}" 
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ml-3 text-sm font-medium text-gray-700">
                        {{ $abogado->name }}
                        @if($abogado->id === $expediente->abogado_responsable_id)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                Responsable
                            </span>
                        @endif
                    </span>
                </label>
            @endforeach
        </div>

        <div class="mt-6 flex justify-end items-center">
            <x-action-message class="mr-3" on="assignments-updated">
                {{ __('Guardado.') }}
            </x-action-message>
            <x-primary-button wire:click="updateAssignments">
                {{ __('Guardar Asignaciones') }}
            </x-primary-button>
        </div>
    </div>

    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Importante:</strong> Los cambios de asignación quedan registrados en la bitácora de seguridad. 
                    El abogado responsable principal siempre tendrá acceso al expediente.
                </p>
            </div>
        </div>
    </div>
</div>
