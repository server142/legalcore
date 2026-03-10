<div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
    <h4 class="font-bold text-gray-700 mb-3 flex items-center">
        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        Programar Nuevo Evento
    </h4>
    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título del Evento</label>
            <input wire:model="titulo" type="text" placeholder="Ej. Audiencia de Pruebas" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('titulo') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Inicio</label>
            <input wire:model="start_time" type="datetime-local" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('start_time') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Fin</label>
            <input wire:model="end_time" type="datetime-local" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('end_time') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipo</label>
            <select wire:model="tipo" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="audiencia">Audiencia</option>
                <option value="cita">Cita / Reunión</option>
                <option value="termino">Término Legal</option>
                <option value="otro">Otro</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Descripción / Notas</label>
            <textarea wire:model="descripcion" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <div class="md:col-span-2 flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-md">
                Guardar Evento
            </button>
        </div>
    </form>
</div>
