<div>
    <x-slot name="header">
        <div class="flex items-center text-sm text-gray-500 uppercase tracking-widest font-bold">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
            Configuración de API & Integraciones
        </div>
    </x-slot>

    <div class="space-y-6 max-w-4xl">
        <!-- Generación de Token -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Generar Nuevo Token</h3>
            <p class="text-gray-500 text-sm mb-6">Usa estos tokens para conectar Diogenes con tu landing page o bots externos. El bot usará los permisos de tu usuario y se identificará automáticamente con tu despacho.</p>
            
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label for="tokenName" class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre del Token (Ej: Bot Landing)</label>
                    <input type="text" id="tokenName" wire:model="tokenName" 
                           class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5 transition-all shadow-sm"
                           placeholder="Nombre descriptivo...">
                    @error('tokenName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <button wire:click="generateToken" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-all shadow-md active:scale-95 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Generar Token
                </button>
            </div>

            @if($newToken)
                <div x-data="{ copied: false }" class="mt-8 p-6 bg-indigo-50 border border-indigo-100 rounded-2xl animate-pulse-once">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-sm font-bold text-indigo-900 uppercase">Copia tu nuevo token 🛡️</h4>
                        <button wire:click="clearNewToken" class="text-indigo-400 hover:text-indigo-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <p class="text-xs text-red-600 font-bold mb-4">IMPORTANTE: Por seguridad, solo verás este código una vez. Cópialo y guárdalo en un lugar seguro.</p>
                    
                    <div class="flex items-center gap-2 bg-white p-3 rounded-xl border border-indigo-200 shadow-inner">
                        <code class="flex-1 font-mono text-sm text-indigo-700 break-all select-all">{{ $newToken }}</code>
                        <button @click="
                                navigator.clipboard.writeText('{{ $newToken }}');
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            " 
                            class="p-2 transition-all rounded-lg"
                            :class="copied ? 'bg-green-100 text-green-600' : 'bg-indigo-100 text-indigo-600 hover:bg-indigo-200'">
                            <span x-show="!copied"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg></span>
                            <span x-show="copied"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Lista de Tokens -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Tokens Activos</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="pb-3 font-bold text-gray-400 uppercase text-[10px]">Nombre</th>
                            <th class="pb-3 font-bold text-gray-400 uppercase text-[10px]">Último Uso</th>
                            <th class="pb-3 font-bold text-gray-400 uppercase text-[10px]">Fecha Creación</th>
                            <th class="pb-3 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($tokens as $token)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 mr-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                        </div>
                                        <span class="font-bold text-gray-700">{{ $token->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 text-gray-500">
                                    {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Nunca usado' }}
                                </td>
                                <td class="py-4 text-gray-500">
                                    {{ $token->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-4 text-right">
                                    <button wire:click="revokeToken({{ $token->id }})" 
                                            wire:confirm="¿Seguro que quieres revocar este token? Los sistemas que lo usen perderán el acceso."
                                            class="text-gray-300 hover:text-red-500 transition-colors p-2"
                                            title="Revocar Acceso">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400 italic">No hay tokens activos. Comienza generando uno arriba.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card de Ayuda -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 rounded-2xl text-white shadow-lg overflow-hidden relative">
            <div class="relative z-10">
                <h3 class="text-xl font-bold mb-2 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    ¿Cómo usar tu Token?
                </h3>
                <p class="text-indigo-50 text-sm mb-4">Copia tu token y agrégalo a la configuración del bot en el encabezado de autorización HTTP.</p>
                <div class="bg-black/20 p-4 rounded-xl border border-white/10 font-mono text-[10px]">
                    <span class="text-indigo-200 uppercase font-bold">Header HTTP</span><br>
                    Authorization: Bearer <span class="text-green-300">TU_TOKEN_AQUÍ</span>
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>
    </div>
</div>
