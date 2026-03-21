<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-2">Comienza a<br>Gestionar.</h2>
        <p class="text-sm text-slate-500 font-medium">Inicia sesión en tu cuenta</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4">
        <!-- Email Address -->
        <div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" /></svg>
                </div>
                <input wire:model="form.email" id="email" 
                    class="block w-full pl-11 pr-4 py-4 input-flat" 
                    type="email" name="email" required autofocus autocomplete="username" placeholder="Tu correo electrónico" />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-1 text-[10px]" />
        </div>

        <!-- Password -->
        <div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                <input wire:model="form.password" id="password" 
                    class="block w-full pl-11 pr-4 py-4 input-flat" 
                    type="password" name="password" required autocomplete="current-password" placeholder="Tu contraseña" />
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1 text-[10px]" />
        </div>

        <div class="flex items-center justify-between px-1">
            <label for="remember" class="flex items-center bg-transparent cursor-pointer group">
                <div class="relative flex items-center justify-center w-5 h-5 mr-2 bg-[#f4f4f5] border-2 border-[#e4e4e7] rounded-md transition-colors group-hover:border-indigo-400">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="absolute w-full h-full opacity-0 cursor-pointer" name="remember">
                    <svg class="w-3 h-3 text-indigo-600 hidden group-has-[:checked]:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-500">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition" href="{{ route('password.request') }}" wire:navigate>
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full btn-flat py-4 text-sm uppercase tracking-wider relative group overflow-hidden">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    Ingresar a mi cuenta
                </span>
            </button>
        </div>
        
        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-500 font-medium">¿Aún no tienes cuenta? 
                <a href="{{ route('register') }}" wire:navigate class="text-indigo-600 font-bold hover:underline">Regístrate aquí</a>
            </p>
        </div>
    </form>
</div>

        <!-- Password -->
        <div class="space-y-1">
            <div class="flex justify-between items-end px-1">
                <label for="password" class="block text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Contraseña</label>
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-bold text-slate-500 hover:text-indigo-400 transition" href="{{ route('password.request') }}" wire:navigate>
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-600 group-focus-within:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                <input wire:model="form.password" id="password" 
                    class="block w-full pl-12 pr-4 py-4 bg-slate-800/50 border border-white/5 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all input-glow" 
                    type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember" class="inline-flex items-center cursor-pointer group">
                <div class="relative">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="sr-only peer" name="remember">
                    <div class="w-10 h-5 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                </div>
                <span class="ms-3 text-xs font-bold text-slate-500 group-hover:text-slate-300 transition-colors">Recordarme</span>
            </label>
        </div>

        <div>
            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black text-sm tracking-widest uppercase shadow-lg shadow-indigo-600/20 active:scale-95 transition-all">
                Iniciar Sesión
            </button>
        </div>

        <div class="pt-4 text-center">
            <p class="text-xs font-bold text-slate-500">
                ¿Aún no tienes cuenta? 
                <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 transition" wire:navigate>Comienza gratis</a>
            </p>
        </div>
    </form>
</div>
