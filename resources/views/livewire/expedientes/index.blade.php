<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Expedientes') }}
    </h2>
</x-slot>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Expedientes</h2>
        <a href="{{ route('expedientes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            + Nuevo Expediente
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <input wire:model.live="search" type="text" placeholder="Buscar por número o título..." class="w-full md:w-1/3 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Abogado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($expedientes as $exp)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $exp->numero }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $exp->titulo }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $exp->cliente->nombre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $exp->abogado->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $exp->estado_procesal }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('expedientes.show', $exp) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                        <button wire:click="cerrar({{ $exp->id }})" wire:confirm="¿Estás seguro de cerrar este expediente?" class="text-red-600 hover:text-red-900">Cerrar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="p-4 border-t">
            {{ $expedientes->links() }}
        </div>
    </div>
</div>
