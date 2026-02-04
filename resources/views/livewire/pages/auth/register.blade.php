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

    public string $planSlug = '';

    public bool $accepted_legal = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'accepted_legal' => ['accepted'],
        ], [
            'accepted_legal.accepted' => 'Debes leer y aceptar el Aviso de Privacidad y los Términos y Condiciones para continuar.',
        ]);

        DB::beginTransaction();

        try {
            // (existing plan selection and tenant creation code...)
            $selectedPlan = Plan::where('slug', $this->planSlug)->first();
            if (!$selectedPlan) { $selectedPlan = Plan::where('slug', 'trial')->first(); }
            $subscriptionStatus = $selectedPlan->slug === 'trial' ? 'trial' : 'pending_payment';
            
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'slug' => Str::slug($validated['company_name']) . '-' . Str::random(6),
                'plan' => $selectedPlan->slug,
                'plan_id' => $selectedPlan->id,
                'trial_ends_at' => now()->addDays($selectedPlan->slug === 'trial' ? 15 : 0),
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

            // Enviar correo de bienvenida profesional
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\UserCreatedMail($user));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error enviando correo de bienvenida (auto-registro): ' . $e->getMessage());
            }

            // 7. Autenticar el usuario
            Auth::login($user);

            // 8. Redirigir según el plan
            if ($selectedPlan->slug !== 'trial') {
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

<div class="w-full">
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Crear cuenta</h2>
        <p class="text-sm text-gray-600">Regístrate para comenzar tu prueba gratuita</p>
    </div>

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nombre Completo')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Company Name -->
        <div class="mt-4">
            <x-input-label for="company_name" :value="__('Nombre de la Empresa/Despacho')" />
            <x-text-input wire:model="company_name" id="company_name" class="block mt-1 w-full" type="text" name="company_name" required autocomplete="organization" />
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Legal Documents Acceptance -->
        <div class="mt-4">
            <label class="flex items-center">
                <input type="checkbox" wire:model="accepted_legal" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">
                    He leído y acepto el <a href="{{ route('privacy') }}" target="_blank" class="text-indigo-600 font-bold hover:underline">Aviso de Privacidad</a> y los <a href="{{ route('terms') }}" target="_blank" class="text-indigo-600 font-bold hover:underline">Términos y Condiciones</a>
                </span>
            </label>
            <x-input-error :messages="$errors->get('accepted_legal')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('¿Ya tienes cuenta?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>
</div>
