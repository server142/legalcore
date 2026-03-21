<?php

/**
 * Rutas del Directorio de Abogados
 * Subdominio: abogados.diogenes.com.mx
 */

use Illuminate\Support\Facades\Route;

// Landing del directorio (página principal del subdominio)
Route::get('/', \App\Livewire\PublicDirectory::class)->name('directory.public');

// Página de registro/publicidad (ANTES del comodín)
Route::get('/unete', function () {
    $plans = \App\Models\Plan::where('is_active', true)
        ->where('slug', 'like', '%directory%')
        ->orderBy('price', 'asc')
        ->get();
    return view('directory.join', compact('plans'));
})->name('directory.advertise');

// Rutas autenticadas del directorio (ANTES del comodín)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard del directorio (analytics, pagos, etc.)
    Route::get('/dashboard', \App\Livewire\Directory\Dashboard::class)->name('directory.dashboard');
});

// Rutas de Sistema para el Subdominio (Asegurar que no las atrape el comodín de perfil)
Route::get('/login', function () {
    return redirect()->route('login');
});

Route::get('/register', function () {
    // Redirigir al registro global manteniendo los parámetros (como el plan)
    return redirect()->to(route('register', request()->query()));
});

// Perfil individual de abogado (COMODÍN AL FINAL)
// Se añade una restricción para que no atrape palabras clave del sistema
Route::get('/{profile}', App\Livewire\PublicDirectoryProfile::class)
    ->where('profile', '^(?!login|register|admin|dashboard|unete).*$')
    ->name('directory.show');
