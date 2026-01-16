<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    Route::get('/expedientes', \App\Livewire\Expedientes\Index::class)->name('expedientes.index');
    Route::get('/expedientes/nuevo', \App\Livewire\Expedientes\Create::class)->name('expedientes.create');
    Route::get('/expedientes/{expediente}', \App\Livewire\Expedientes\Show::class)->name('expedientes.show');

    Route::get('/clientes', \App\Livewire\Clientes\Index::class)->name('clientes.index');
    Route::get('/clientes/nuevo', \App\Livewire\Clientes\Create::class)->name('clientes.create');

    Route::get('/agenda', \App\Livewire\Agenda\Index::class)->name('agenda.index');
    Route::get('/terminos', \App\Livewire\Terminos\Index::class)->name('terminos.index');
    Route::get('/facturacion', \App\Livewire\Facturacion\Index::class)->name('facturacion.index');

    Route::get('/documentos/{documento}', [\App\Http\Controllers\DocumentController::class, 'show'])->name('documentos.show');
    Route::get('/bitacora', \App\Livewire\Audit\Index::class)->name('audit.index');
    
    Route::get('/mensajes', \App\Livewire\Mensajes\Index::class)->name('mensajes.index');
    Route::get('/expedientes/{expediente}/asignaciones', \App\Livewire\Expedientes\ManageAssignments::class)->name('expedientes.assignments')->middleware('can:manage users');
    
    // Admin only - Trial Management
    Route::get('/admin/trials', \App\Livewire\Admin\TrialManagement::class)->name('admin.trials')->middleware('can:manage tenants');
    Route::get('/admin/users', \App\Livewire\Admin\Users\Index::class)->name('admin.users.index')->middleware('can:manage users');
    Route::get('/admin/roles', \App\Livewire\Admin\Roles\Index::class)->name('admin.roles.index')->middleware('can:manage users');
    Route::get('/admin/materias', \App\Livewire\Admin\Materias\Index::class)->name('admin.materias.index')->middleware('can:manage users');
    Route::get('/admin/juzgados', \App\Livewire\Admin\Juzgados\Index::class)->name('admin.juzgados.index')->middleware('can:manage users');
    Route::get('/admin/abogados', \App\Livewire\Admin\Abogados\Index::class)->name('admin.abogados.index')->middleware('can:manage users');
    Route::get('/admin/settings', \App\Livewire\Admin\TenantSettings::class)->name('admin.settings')->middleware('can:manage settings');

    Route::get('/reportes/factura/{factura}', [\App\Http\Controllers\ReportController::class, 'invoice'])->name('reportes.factura');
    Route::get('/reportes/expediente/{expediente}', [\App\Http\Controllers\ReportController::class, 'expediente'])->name('reportes.expediente');

    Route::get('/manual', \App\Livewire\Manual\Index::class)->name('manual.index');
    Route::get('/admin/manual', \App\Livewire\Manual\Manage::class)->name('manual.manage')->middleware('can:manage users');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
