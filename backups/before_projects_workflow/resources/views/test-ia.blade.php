<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Prueba Aislada de IA - Expediente: {{ $expediente->numero }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center h-[500px] flex items-center justify-center">
                
                <div class="space-y-4">
                    <p class="text-gray-600">Esta es una página vacía diseñada para probar el panel de IA sin interferencias de otros elementos.</p>
                    
                    <button x-data 
                            @click="$dispatch('toggle-ai-assistant')" 
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold shadow-lg hover:bg-indigo-700 transition transform hover:scale-105">
                        Abrir Asistente "Diogenes"
                    </button>
                    
                    <p class="text-sm text-gray-400 mt-4">Pulse el botón para desplegar el panel lateral.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- El componente bajo prueba -->
    <livewire:expedientes.ai-assistant :expediente="$expediente" />

</x-app-layout>
