<div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
    <h4 class="font-bold text-gray-700 mb-3">Registrar Nueva Actuación</h4>
    <form wire:submit.prevent="save" class="space-y-3">
        <div>
            <label class="block text-xs font-medium text-gray-700">Título de la Actuación</label>
            <input wire:model="titulo" type="text" placeholder="Ej. Se recibe notificación de amparo" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('titulo') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
        </div>
        
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700">Fecha</label>
                <input wire:model="fecha" type="date" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700">¿Es un plazo legal?</label>
                <div class="mt-2 flex items-center">
                    <input wire:model.live="es_plazo" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2 text-xs text-gray-600">Sí, tiene vencimiento</span>
                </div>
            </div>
        </div>

        @if($es_plazo)
        <div>
            <label class="block text-xs font-medium text-gray-700">Fecha de Vencimiento</label>
            <input wire:model="fecha_vencimiento" type="date" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        @endif

        <div>
            <label class="block text-xs font-medium text-gray-700">Descripción / Resumen</label>
            <textarea wire:model="descripcion" rows="2" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-sm font-medium hover:bg-indigo-700 transition">
                Guardar Actuación
            </button>
        </div>
    </form>
</div>
