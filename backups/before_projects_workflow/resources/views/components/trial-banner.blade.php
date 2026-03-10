@if(auth()->user()->tenant->isOnTrial())
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-xl shadow-lg mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Período de Prueba Activo</h3>
                    <p class="text-sm opacity-90">
                        Te quedan <span class="font-bold text-xl">{{ auth()->user()->tenant->daysLeftInTrial() }}</span> días de prueba gratuita
                    </p>
                </div>
            </div>
            <a href="#pricing" class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-bold hover:shadow-xl transition">
                Ver Planes
            </a>
        </div>
        <div class="mt-4 bg-white/10 rounded-full h-2">
            <div class="bg-white h-2 rounded-full transition-all" style="width: {{ (auth()->user()->tenant->daysLeftInTrial() / 30) * 100 }}%"></div>
        </div>
    </div>
@elseif(auth()->user()->tenant->trialExpired())
    <div class="bg-red-500 text-white p-6 rounded-xl shadow-lg mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Tu Prueba Gratuita Ha Expirado</h3>
                    <p class="text-sm opacity-90">Selecciona un plan para continuar usando Diogenes</p>
                </div>
            </div>
            <a href="/upgrade" class="bg-white text-red-600 px-6 py-3 rounded-lg font-bold hover:shadow-xl transition">
                Actualizar Ahora
            </a>
        </div>
    </div>
@endif
