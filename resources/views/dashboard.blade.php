<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(auth()->user()->hasRole('super_admin'))
                <livewire:dashboards.super-admin />
            @elseif(auth()->user()->hasRole(['admin', 'abogado']))
                <livewire:dashboards.tenant-admin />
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-red-600 font-bold text-center">
                        {{ __("El acceso al Dashboard está restringido.") }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
