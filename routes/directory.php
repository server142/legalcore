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
    return view('directory.join');
})->name('directory.advertise');

// Rutas autenticadas del directorio (ANTES del comodín)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard del directorio (analytics, pagos, etc.)
    Route::get('/dashboard', \App\Livewire\Directory\Dashboard::class)->name('directory.dashboard');
});

// Perfil individual de abogado (COMODÍN AL FINAL)
Route::get('/{profile}', App\Livewire\PublicDirectoryProfile::class)->name('directory.show');
