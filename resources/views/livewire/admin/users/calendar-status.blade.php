<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Estado de Correos de Calendario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 font-medium">Total Usuarios</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 font-medium">Configurados</p>
                            <p class="text-2xl font-bold text-green-600">{{ $stats['configured'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500 font-medium">Sin Configurar</p>
                            <p class="text-2xl font-bold text-red-600">{{ $stats['missing'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow mb-6 p-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input wire:model.live="search" type="text" placeholder="Buscar por nombre o email..." class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>
                    <div>
                        <select wire:model.live="filterStatus" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="all">Todos</option>
                            <option value="configured">Configurados</option>
                            <option value="missing">Sin Configurar</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla de Usuarios -->
            <div class="bg-white rounded-lg shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full min-w-[900px] divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Sistema</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Calendario</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-indigo-600 font-medium text-sm">{{ substr($user->name, 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    <div class="text-sm text-gray-900 break-all">{{ $user->email }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-4">
                                    @if($user->calendar_email)
                                        <div class="text-sm text-gray-900 break-all">{{ $user->calendar_email }}</div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No configurado</span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    @if($user->calendar_email)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Configurado
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @foreach($user->roles as $role)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 sm:px-6 py-4 text-center text-gray-500">
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            </div>

            <!-- Nota informativa -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Nota:</strong> Los usuarios deben configurar su correo de calendario en su perfil para recibir invitaciones automáticas de Google Calendar cuando se les asigne a eventos de expedientes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
