<?php

use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $company_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount()
    {
        $this->planSlug = request()->query('plan', 'trial');
    }

    #[Url]
    public string $planSlug = '';

    public bool $accepted_legal = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'accepted_legal' => ['accepted'],
        ], [
            'accepted_legal.accepted' => 'Debes leer y aceptar el Aviso de Privacidad y los Términos y Condiciones para continuar.',
        ]);

        DB::beginTransaction();

        try {
            // Generar nombre de empresa temporal
            $companyName = 'Despacho de ' . $validated['name'];
            
            $selectedPlan = Plan::where('slug', $this->planSlug)->first();
            if (!$selectedPlan) { $selectedPlan = Plan::where('slug', 'trial')->first(); }
            
            // Determine initial status
            $subscriptionStatus = 'pending_payment';
            if ($selectedPlan->slug === 'trial') {
                $subscriptionStatus = 'trial';
            } elseif ($selectedPlan->slug === 'directory-free' || $selectedPlan->price <= 0) {
                $subscriptionStatus = 'active';
            }
            
            $tenant = Tenant::create([
                'name' => $companyName,
                'slug' => Str::slug($companyName) . '-' . Str::random(6),
                'plan' => $selectedPlan->slug,
                'plan_id' => $selectedPlan->id,
                'trial_ends_at' => $selectedPlan->slug === 'trial' ? now()->addDays($selectedPlan->duration_in_days ?? 15) : null,
                'subscription_status' => $subscriptionStatus,
                'is_active' => true,
            ]);

            // 2.5 Crear contratos por default para el Despacho
            \App\Services\LegalContentService::createTenantDefaults($tenant->id);

            // 3. Crear el usuario e asignarlo al tenant
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'tenant_id' => $tenant->id,
                'status' => 'active',
            ]);

            // 4. Registrar aceptación legal
            $legalDocs = \App\Models\LegalDocument::whereIn('tipo', ['PRIVACIDAD', 'TERMINOS'])
                ->where('activo', true)
                ->get();
            
            foreach ($legalDocs as $doc) {
                \App\Models\LegalAcceptance::create([
                    'user_id' => $user->id,
                    'legal_document_id' => $doc->id,
                    'version' => $doc->version,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            // 5. Asignar rol de admin al primer usuario
            $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            $user->assignRole($adminRole);

            DB::commit();

            // 6. Disparar evento de registro
            event(new Registered($user));

            // Enviar correo de bienvenida profesional (se encolará para mayor rapidez)
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\UserCreatedMail($user));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error encolando correo de bienvenida (auto-registro): ' . $e->getMessage());
            }

            // 7. Autenticar el usuario
            Auth::login($user);

            // 8. Redirigir según el plan
            if ($selectedPlan && str_contains($selectedPlan->slug, 'directory')) {
                 $this->redirect(route('profile.directory'), navigate: true);
            } elseif ($selectedPlan->slug !== 'trial' && $subscriptionStatus !== 'active') {
                $this->redirect(route('billing.subscribe', ['plan' => $selectedPlan->slug]), navigate: true);
            } else {
                $this->redirect(route('dashboard', absolute: false), navigate: true);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al crear la cuenta: ' . $e->getMessage());
            throw $e;
        }
    }
}; ?>

@php
    $maxWidth = 'sm:max-w-xl md:max-w-4xl'; 
@endphp

<div>
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Crear cuenta</h2>
        @if($planSlug === 'directory-free')
            <p class="text-xs font-black text-indigo-600 uppercase tracking-widest bg-indigo-500/5 py-2 rounded-full inline-block px-4 border border-indigo-500/10 mb-2">Plan Directorio Gratuito</p>
        @endif
        <p class="text-sm text-slate-500 font-medium tracking-wide">Comienza tu prueba gratuita hoy</p>
    </div>

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 text-xs rounded-2xl flex items-center gap-2 font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="register" class="space-y-4">
        <input type="hidden" wire:model="planSlug">
        
        <!-- Name -->
        <div class="space-y-1">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <input wire:model="name" id="name" 
                    class="block w-full pl-11 pr-4 py-4 input-custom rounded-2xl text-sm placeholder-slate-400" 
                    type="text" name="name" required autofocus placeholder="Tu nombre completo" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-[10px]" />
        </div>

        <!-- Email Address -->
        <div class="space-y-1">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <input wire:model="email" id="email" 
                    class="block w-full pl-11 pr-4 py-4 input-custom rounded-2xl text-sm placeholder-slate-400" 
                    type="email" name="email" required placeholder="Correo electrónico" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-[10px]" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Password -->
            <div class="space-y-1">
                <input wire:model="password" id="password" 
                    class="block w-full px-4 py-4 input-custom rounded-2xl text-sm placeholder-slate-400" 
                    type="password" name="password" required placeholder="Contraseña" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-[10px]" />
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1">
                <input wire:model="password_confirmation" id="password_confirmation" 
                    class="block w-full px-4 py-4 input-custom rounded-2xl text-sm placeholder-slate-400" 
                    type="password" name="password_confirmation" required placeholder="Confirmar" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-[10px]" />
            </div>
        </div>

        <!-- Legal Documents Acceptance -->
        <div class="pt-2">
            <label class="flex items-center cursor-pointer group">
                <input type="checkbox" wire:model="accepted_legal" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-600/20">
                <span class="ms-3 text-[10px] font-semibold text-slate-500 group-hover:text-slate-700 transition-colors leading-tight">
                    Acepto el <a href="{{ route('privacy') }}" target="_blank" class="text-indigo-600 font-bold">Aviso de Privacidad</a> y los <a href="{{ route('terms') }}" target="_blank" class="text-indigo-600 font-bold">Términos y Condiciones</a>
                </span>
            </label>
            <x-input-error :messages="$errors->get('accepted_legal')" class="mt-1 text-[10px]" />
        </div>

        <div class="pt-4 flex flex-col gap-4">
            <button type="submit" class="w-full py-4 btn-primary-custom rounded-2xl font-bold text-sm shadow-lg active:scale-95 transition-all" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="register">Registrarse</span>
                <span wire:loading wire:target="register">Procesando...</span>
            </button>
            <div class="text-center">
                <a class="text-xs font-bold text-slate-400 hover:text-slate-600 transition" href="{{ route('login') }}" wire:navigate>
                    ¿Ya tienes una cuenta? <span class="text-indigo-600">Entra aquí</span>
                </a>
            </div>
        </div>
    </form>
</div>
