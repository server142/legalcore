<?php

/**
 * Rutas del Directorio de Abogados
 * Subdominio: abogados.diogenes.com.mx
 */

use Illuminate\Support\Facades\Route;

// Landing del directorio (página principal del subdominio)
Route::get('/', \App\Livewire\PublicDirectory::class)->name('directory.public');

// Perfil individual de abogado
Route::get('/{profile}', App\Livewire\PublicDirectoryProfile::class)->name('directory.show');

// Página de registro/publicidad
Route::get('/unete', function () {
    return view('directory.join');
})->name('directory.advertise');

// Rutas autenticadas del directorio
Route::middleware(['auth', 'verified'])->group(function () {
    // Gestión del perfil público del abogado
    Route::get('/perfil', \App\Livewire\Profile\DirectoryManager::class)->name('profile.directory');
    
    // Dashboard del directorio (analytics, pagos, etc.)
    Route::get('/dashboard', \App\Livewire\Directory\Dashboard::class)->name('directory.dashboard');
});
