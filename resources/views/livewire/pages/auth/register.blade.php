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
    <div class="mb-8">
        @if($planSlug === 'directory-free')
            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest bg-indigo-50 py-1.5 px-3 rounded-full inline-block border border-indigo-100 mb-4">Directorio Gratuito</p>
        @else
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Completar Registro</h3>
        @endif
        <h2 class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-2">Crea tu Cuenta.</h2>
        <p class="text-sm text-slate-500 font-medium">Únete a cientos de despachos</p>
    </div>

    @if (session('error'))
        <div class="mb-4 bg-red-50 border-auth-card border-red-200 text-red-600 px-4 py-3 rounded-2xl text-xs font-semibold shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="register" class="space-y-4">
        <!-- Name -->
        <div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <input wire:model="name" id="name" 
                    class="block w-full pl-11 pr-4 py-4 input-flat" 
                    type="text" name="name" required autofocus autocomplete="name" placeholder="Tu nombre completo" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-[10px]" />
        </div>

        <!-- Email Address -->
        <div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" /></svg>
                </div>
                <input wire:model="email" id="email" 
                    class="block w-full pl-11 pr-4 py-4 input-flat" 
                    type="email" name="email" required autocomplete="username" placeholder="Correo electrónico" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-[10px]" />
        </div>

        <div class="grid grid-cols-2 gap-3">
            <!-- Password & Confirm -->
            <div>
                <input wire:model="password" id="password" 
                    class="block w-full px-4 py-4 input-flat" 
                    type="password" name="password" required autocomplete="new-password" placeholder="Contraseña" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-[10px]" />
            </div>

            <div>
                <input wire:model="password_confirmation" id="password_confirmation" 
                    class="block w-full px-4 py-4 input-flat" 
                    type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmar" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-[10px]" />
            </div>
        </div>

        <div class="pt-2">
            <label for="accepted_legal" class="flex items-start bg-transparent cursor-pointer group">
                <div class="relative flex-shrink-0 flex items-center justify-center w-5 h-5 mr-3 mt-0.5 bg-[#f4f4f5] border-2 border-[#e4e4e7] rounded-md transition-colors group-hover:border-indigo-400">
                    <input wire:model="accepted_legal" id="accepted_legal" type="checkbox" class="absolute w-full h-full opacity-0 cursor-pointer" name="accepted_legal">
                    <svg class="w-3 h-3 text-indigo-600 hidden group-has-[:checked]:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-slate-500 leading-snug">
                    Acepto el <a href="{{ route('privacy') }}" target="_blank" class="text-indigo-600 font-bold hover:underline">Aviso de Privacidad</a> y los <a href="{{ route('terms') }}" target="_blank" class="text-indigo-600 font-bold hover:underline">Términos y Condiciones</a>
                </span>
            </label>
            <x-input-error :messages="$errors->get('accepted_legal')" class="mt-1 text-[10px]" />
        </div>


        <div class="mt-6">
            <button type="submit" class="w-full btn-flat py-4 text-sm uppercase tracking-wider relative group overflow-hidden" wire:loading.attr="disabled">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="register">Completar Registro</span>
                    <span wire:loading wire:target="register">Procesando...</span>
                </span>
            </button>
        </div>
        
        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-500 font-medium">¿Ya tienes cuenta? 
                <a href="{{ route('login') }}" wire:navigate class="text-indigo-600 font-bold hover:underline">Ingresa aquí</a>
            </p>
        </div>
    </form>
</div>
