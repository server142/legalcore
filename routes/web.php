<?php

use Illuminate\Support\Facades\Route;
use App\Models\Plan;

Route::get('/', function () {
    $plans = Plan::where('is_active', true)
        ->where('slug', '!=', 'trial') // Opcional: Ocultar trial si solo queremos mostrar planes de pago
        ->orderBy('price', 'asc')
        ->get();
    return view('welcome', ['plans' => $plans]);
})->name('welcome');

// Public Legal Pages (Required by Google API Verification)
Route::get('/privacy', function () {
    $content = file_get_contents(base_path('POLITICA_PRIVACIDAD.md'));
    return view('legal.document', ['title' => 'Política de Privacidad', 'content' => $content]);
})->name('privacy');

Route::get('/terms', function () {
    $content = file_get_contents(base_path('TERMINOS_SERVICIO.md'));
    return view('legal.document', ['title' => 'Términos de Servicio', 'content' => $content]);
})->name('terms');

Route::get('/cita/{token}', [\App\Http\Controllers\PublicAsesoriaController::class, 'show'])->name('asesorias.public');
Route::get('/qr/asesoria/{token}', [\App\Http\Controllers\PublicAsesoriaQrController::class, 'show'])->name('asesorias.public.qr');

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
    
    // Asesorías
    Route::get('/asesorias', \App\Livewire\Asesorias\Index::class)->name('asesorias.index');
    Route::get('/asesorias/nueva', \App\Livewire\Asesorias\Form::class)->name('asesorias.create');
    Route::get('/asesorias/{asesoria}/editar', \App\Livewire\Asesorias\Form::class)->name('asesorias.edit');

    Route::get('/documentos/{documento}', [\App\Http\Controllers\DocumentController::class, 'show'])->name('documentos.show');
    Route::get('/bitacora', \App\Livewire\Audit\Index::class)->name('audit.index');
    
    Route::get('/mensajes', \App\Livewire\Mensajes\Index::class)->name('mensajes.index');
    Route::get('/expedientes/{expediente}/asignaciones', \App\Livewire\Expedientes\ManageAssignments::class)->name('expedientes.assignments')->middleware('can:manage users');
    
    // Admin only - Trial Management
    // Admin - Tenant Management
    Route::get('/admin/tenants', \App\Livewire\Admin\Tenants\Index::class)->name('admin.tenants.index')->middleware('can:manage tenants');
    Route::get('/admin/users', \App\Livewire\Admin\Users\Index::class)->name('admin.users.index')->middleware('can:manage users');
    Route::get('/admin/users/calendar-status', \App\Livewire\Admin\Users\CalendarStatus::class)->name('admin.users.calendar-status')->middleware('can:manage users');
    Route::get('/admin/roles', \App\Livewire\Admin\Roles\Index::class)->name('admin.roles.index')->middleware('can:manage users');
    Route::get('/admin/materias', \App\Livewire\Admin\Materias\Index::class)->name('admin.materias.index')->middleware('can:manage users');
    Route::get('/admin/estados-procesales', \App\Livewire\Admin\EstadosProcesales\Index::class)->name('admin.estados-procesales.index');
    Route::get('/admin/juzgados', \App\Livewire\Admin\Juzgados\Index::class)->name('admin.juzgados.index')->middleware('can:manage users');
    Route::get('/admin/abogados', \App\Livewire\Admin\Abogados\Index::class)->name('admin.abogados.index')->middleware('can:manage users');
    Route::get('/admin/settings', \App\Livewire\Admin\TenantSettings::class)->name('admin.settings')->middleware('can:manage settings');
    Route::get('/admin/reports/income', \App\Livewire\Admin\Reports\IncomeReport::class)->name('admin.reports.income')->middleware('can:manage tenants');
    Route::get('/admin/global-settings', \App\Livewire\Admin\GlobalSettings::class)->name('admin.global-settings')->middleware('can:manage tenants');
    
    // Plans Management
    Route::get('/admin/plans', \App\Livewire\Admin\Plans\Index::class)->name('admin.plans.index')->middleware('can:manage settings');
    Route::get('/admin/plans/create', \App\Livewire\Admin\Plans\Manage::class)->name('admin.plans.create')->middleware('can:manage settings');
    Route::get('/admin/plans/{plan}/edit', \App\Livewire\Admin\Plans\Manage::class)->name('admin.plans.edit')->middleware('can:manage settings');

    Route::get('/reportes/factura/{factura}', [\App\Http\Controllers\ReportController::class, 'invoice'])->name('reportes.factura');
    Route::get('/reportes/expediente/{expediente}', [\App\Http\Controllers\ReportController::class, 'expediente'])->name('reportes.expediente');
    Route::get('/reportes/ingresos', \App\Livewire\Reports\IncomeReport::class)->name('reportes.ingresos')->middleware('can:manage billing');

    Route::get('/manual', \App\Livewire\Manual\Index::class)->name('manual.index');
    Route::get('/admin/manual', \App\Livewire\Manual\Manage::class)->name('manual.manage')->middleware('can:manage users');

    // Subscription Routes
    Route::get('/billing/subscribe/{plan}', \App\Livewire\Billing\Subscribe::class)->name('billing.subscribe');
    Route::get('/subscription/expired', [\App\Http\Controllers\SubscriptionController::class, 'expired'])->name('subscription.expired');

    // Google Calendar Routes
    Route::get('auth/google', [\App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [\App\Http\Controllers\GoogleController::class, 'handleGoogleCallback']);
    Route::post('auth/google/disconnect', [\App\Http\Controllers\GoogleController::class, 'disconnect'])->name('auth.google.disconnect');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
