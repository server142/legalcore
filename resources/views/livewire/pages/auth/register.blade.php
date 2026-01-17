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

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();

        try {
            // 1. Obtener el plan seleccionado
            $selectedPlan = Plan::where('slug', $this->planSlug)->first();
            
            // Si no existe el plan o es inválido, fallback a trial
            if (!$selectedPlan) {
                $selectedPlan = Plan::where('slug', 'trial')->first();
            }

            // 2. Crear el Tenant
            // Si es trial, status es trial. Si es de pago, status es 'pending_payment' hasta que pague
            $subscriptionStatus = $selectedPlan->slug === 'trial' ? 'trial' : 'pending_payment';
            
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'slug' => Str::slug($validated['company_name']) . '-' . Str::random(6),
                'plan' => $selectedPlan->slug,
                'plan_id' => $selectedPlan->id,
                'trial_ends_at' => now()->addDays($selectedPlan->slug === 'trial' ? 15 : 0), // Solo trial tiene trial days gratis sin tarjeta
                'subscription_status' => $subscriptionStatus,
                'is_active' => true, // Permitimos login, pero middleware restringirá acceso si pending_payment
            ]);

            // 3. Crear el usuario y asignarlo al tenant
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'tenant_id' => $tenant->id,
                'status' => 'active',
            ]);

            // 4. Asignar rol de admin al primer usuario
            $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            $user->assignRole($adminRole);

            DB::commit();

            // 5. Disparar evento de registro
            event(new Registered($user));

            // 6. Autenticar el usuario
            Auth::login($user);

            // 7. Redirigir según el plan
            if ($selectedPlan->slug !== 'trial') {
                // Si eligió un plan de pago, lo mandamos a configurar su suscripción
                $this->redirect(route('billing.subscribe', ['plan' => $selectedPlan->slug]), navigate: true);
            } else {
                // Si es trial, al dashboard directo
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

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('¿Ya tienes cuenta?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>
</div>
