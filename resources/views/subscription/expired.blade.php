<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Suscripción Vencida') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-24 w-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-2">¡Tu suscripción ha expirado!</h3>
                    
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 inline-block" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
                        Tu periodo de prueba o suscripción ha finalizado. Para continuar disfrutando de todas las funcionalidades de LegalCore y gestionar tus expedientes sin interrupciones, por favor actualiza tu plan.
                    </p>

                    <div class="flex justify-center space-x-4">
                        <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition duration-150 ease-in-out transform hover:scale-105 shadow-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Renovar Suscripción
                        </a>
                        
                        <a href="{{ route('dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-lg transition">
                            Volver al Inicio
                        </a>
                    </div>
                    
                    <p class="mt-8 text-sm text-gray-500">
                        ¿Necesitas ayuda? Contacta a <a href="mailto:soporte@legalcore.com" class="text-indigo-600 hover:underline">soporte@legalcore.com</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
