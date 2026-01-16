<div class="p-6">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Bienvenido, {{ auth()->user()->name }}</h2>
        <p class="text-gray-600">Aquí puede consultar el estado de sus asuntos legales.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-700">Mis Expedientes</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($expedientes as $exp)
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-indigo-600">{{ $exp->numero }}</h4>
                            <p class="text-sm text-gray-800 font-medium">{{ $exp->titulo }}</p>
                            <p class="text-xs text-gray-500">{{ $exp->materia }} | {{ $exp->juzgado }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $exp->estado_procesal }}
                        </span>
                    </div>
                    <div class="mt-2">
                        <p class="text-xs text-gray-600">
                            <strong>Último movimiento:</strong> 
                            {{ $exp->actuaciones()->latest()->first()?->titulo ?? 'Sin movimientos recientes' }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-gray-500">
                    No tiene expedientes registrados.
                </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-700">Documentos Recientes</h3>
            </div>
            <div class="p-4">
                <p class="text-sm text-gray-500 italic text-center">Próximamente podrá descargar sus documentos aquí.</p>
            </div>
        </div>
    </div>
</div>
