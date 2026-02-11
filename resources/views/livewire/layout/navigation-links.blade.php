<nav class="space-y-1">
    <!-- Main Section -->
    <div class="pb-4">
        <p x-show="!sidebarCollapsed" x-transition.opacity class="px-2 mb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest whitespace-nowrap overflow-hidden">Principal</p>
        
        @if(auth()->user()->hasRole(['super_admin', 'admin', 'abogado']))
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="dashboard">
            {{ __('Dashboard') }}
        </x-sidebar-link>
        @endif

        <x-sidebar-link :href="route('expedientes.index')" :active="request()->routeIs('expedientes.*')" icon="folder">
            {{ __('Expedientes') }}
        </x-sidebar-link>

        @can('view terminos')
        <x-sidebar-link :href="route('terminos.index')" :active="request()->routeIs('terminos.*')" icon="calendar">
            {{ __('Términos') }}
        </x-sidebar-link>
        @endcan

        <x-sidebar-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')" icon="users">
            {{ __('Clientes') }}
        </x-sidebar-link>

        @can('view agenda')
        <x-sidebar-link :href="route('agenda.index')" :active="request()->routeIs('agenda.*')" icon="calendar">
            {{ __('Agenda') }}
        </x-sidebar-link>
        @endcan

        <x-sidebar-link :href="route('asesorias.index')" :active="request()->routeIs('asesorias.*')" icon="chat">
            {{ __('Asesorías') }}
        </x-sidebar-link>

        <x-sidebar-link :href="route('ai.assistant')" :active="request()->routeIs('ai.assistant')" icon="sparkles">
            {{ __('Asistente IA') }}
        </x-sidebar-link>

        <x-sidebar-link :href="route('dof.index')" :active="request()->routeIs('dof.index')" icon="dof">
            {{ __('Diario Oficial') }}
        </x-sidebar-link>

        <x-sidebar-link :href="route('sjf.index')" :active="request()->routeIs('sjf.index')" icon="document-text">
            {{ __('Jurisprudencia') }}
        </x-sidebar-link>

        @if(auth()->user()->can('view_audit_logs'))
        <x-sidebar-link :href="route('facturacion.index')" :active="request()->routeIs('facturacion.*')" icon="billing">
            {{ __('Facturación') }}
        </x-sidebar-link>
        @endif

        <x-sidebar-link :href="route('manual.index')" :active="request()->routeIs('manual.*')" icon="book">
            {{ __('Manual') }}
        </x-sidebar-link>

        <x-sidebar-link :href="route('library.index')" :active="request()->routeIs('library.*')" icon="collection">
            {{ __('Formatos') }}
        </x-sidebar-link>

        @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin'))
        <x-sidebar-link :href="route('audit.index')" :active="request()->routeIs('audit.*')" icon="audit">
            {{ __('Bitácora') }}
        </x-sidebar-link>
        @endif
    </div>

    <!-- SaaS Section -->
    @if(auth()->user()->hasRole('super_admin'))
    <div class="pt-4 border-t border-gray-300">
        <p x-show="!sidebarCollapsed" x-transition.opacity class="px-2 mb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest whitespace-nowrap overflow-hidden">SaaS</p>
        
        <x-sidebar-link :href="route('admin.tenants.index')" :active="request()->routeIs('admin.tenants.*')" icon="office-building">
            {{ __('Tenants') }}
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.plans.index')" :active="request()->routeIs('admin.plans.*')" icon="collection">
            {{ __('Planes') }}
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.global-settings')" :active="request()->routeIs('admin.global-settings')" icon="settings">
            {{ __('Configuración Global') }}
        </x-sidebar-link>
        
        <x-sidebar-link :href="route('admin.announcements')" :active="request()->routeIs('admin.announcements')" icon="speakerphone">
            {{ __('Anuncios') }}
        </x-sidebar-link>
    </div>
    @endif

    <!-- Administration Section -->
    @canany(['manage settings', 'manage users'])
    <div class="pt-4 border-t border-gray-300">
        <p x-show="!sidebarCollapsed" x-transition.opacity class="px-2 mb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest whitespace-nowrap overflow-hidden">Administración</p>
        
        @can('manage settings')
        <x-sidebar-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')" icon="settings">
            {{ __('Configuración') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('admin.legal-documents.index')" :active="request()->routeIs('admin.legal-documents.*')" icon="book">
            {{ __('Documentos Legales') }}
        </x-sidebar-link>
        @endcan

        @can('manage users')
        <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="user-group">
            {{ __('Usuarios') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('admin.users.calendar-status')" :active="request()->routeIs('admin.users.calendar-status')" icon="calendar">
            {{ __('Estado Calendarios') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')" icon="user-group">
            {{ __('Roles') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('admin.abogados.index')" :active="request()->routeIs('admin.abogados.*')" icon="briefcase">
            {{ __('Abogados') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('admin.materias.index')" :active="request()->routeIs('admin.materias.*')" icon="book">
            {{ __('Materias') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('admin.estados-procesales.index')" :active="request()->routeIs('admin.estados-procesales.*')" icon="book">
            {{ __('Estados Procesales') }}
        </x-sidebar-link>
        <x-sidebar-link :href="route('admin.juzgados.index')" :active="request()->routeIs('admin.juzgados.*')" icon="court">
            {{ __('Juzgados') }}
        </x-sidebar-link>
        @endcan
    </div>
    @endcanany
</nav>
