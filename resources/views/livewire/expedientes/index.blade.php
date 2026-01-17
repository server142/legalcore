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
        
        <!-- Desktop Table -->
        <table class="min-w-full divide-y divide-gray-200 hidden md:table">
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

        <!-- Mobile Cards -->
        <div class="block md:hidden">
            @foreach($expedientes as $exp)
            <div class="p-4 border-b border-gray-200 hover:bg-gray-50 transition">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-wide">{{ $exp->numero }}</span>
                        <h3 class="text-lg font-bold text-gray-900">{{ $exp->titulo }}</h3>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $exp->estado_procesal }}
                    </span>
                </div>
                
                <div class="space-y-1 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        {{ $exp->cliente->nombre }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        {{ $exp->abogado->name }}
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('expedientes.show', $exp) }}" class="text-indigo-600 font-medium text-sm hover:text-indigo-800">Ver Detalles</a>
                    <button wire:click="cerrar({{ $exp->id }})" wire:confirm="¿Estás seguro de cerrar este expediente?" class="text-red-600 font-medium text-sm hover:text-red-800">Cerrar</button>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="p-4 border-t">
            {{ $expedientes->links() }}
        </div>
    </div>
</div>
