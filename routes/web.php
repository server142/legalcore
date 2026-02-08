<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Plan;
use App\Models\Expediente; // Added for the moved route

// Public Legal Pages (Required by Google API Verification)

Route::get('/', function () {
    $plans = Plan::where('is_active', true)
        ->where('slug', '!=', 'trial')
        ->where('slug', '!=', 'exento')
        ->orderBy('price', 'asc')
        ->get();
    
    $legalDocs = \App\Models\LegalDocument::where('activo', true)
        ->whereJsonContains('visible_en', 'footer')
        ->get();

    return view('welcome', [
        'plans' => $plans,
        'legalDocs' => $legalDocs
    ]);
})->name('welcome');

// Public Legal Pages (Required by Google API Verification)
Route::get('/legal/acceptance', \App\Livewire\Legal\AcceptanceMandatory::class)->name('legal.acceptance')->middleware('auth');
Route::get('/privacy', [\App\Http\Controllers\LegalDocumentController::class, 'show'])->defaults('type', 'PRIVACIDAD')->name('privacy');
Route::get('/terms', [\App\Http\Controllers\LegalDocumentController::class, 'show'])->defaults('type', 'TERMINOS')->name('terms');
Route::get('/legal/{type}', [\App\Http\Controllers\LegalDocumentController::class, 'show'])->name('legal.view');

Route::get('/cita/{token}', [\App\Http\Controllers\PublicAsesoriaController::class, 'show'])->name('asesorias.public');
Route::get('/qr/asesoria/{token}', [\App\Http\Controllers\PublicAsesoriaQrController::class, 'show'])->name('asesorias.public.qr');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    Route::get('/expedientes', \App\Livewire\Expedientes\Index::class)->name('expedientes.index');
    Route::get('/expedientes/nuevo', \App\Livewire\Expedientes\Create::class)->name('expedientes.create');
    Route::get('/expedientes/{expediente}/contract', [App\Http\Controllers\ContractController::class, 'generate'])->name('expedientes.contract');
    Route::get('/expedientes/{expediente}', \App\Livewire\Expedientes\Show::class)->name('expedientes.show');

    Route::get('/clientes', \App\Livewire\Clientes\Index::class)->name('clientes.index');
    Route::get('/clientes/nuevo', \App\Livewire\Clientes\Form::class)->name('clientes.create');
    Route::get('/clientes/{cliente}/editar', \App\Livewire\Clientes\Form::class)->name('clientes.edit');

    Route::get('/agenda', \App\Livewire\Agenda\Index::class)->name('agenda.index');
    Route::get('/terminos', \App\Livewire\Terminos\Index::class)->name('terminos.index');
    Route::get('/facturacion', \App\Livewire\Facturacion\Index::class)->name('facturacion.index');
    
    // Asesorías
    Route::get('/asesorias', \App\Livewire\Asesorias\Index::class)->name('asesorias.index');
    Route::get('/asesorias/nueva', \App\Livewire\Asesorias\Form::class)->name('asesorias.create');
    Route::get('/asesorias/{asesoria}/editar', \App\Livewire\Asesorias\Form::class)->name('asesorias.edit');

    /*
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    */

    // RUTA DE PRUEBA AISLADA PARA IA
    Route::get('/test-ia/{expediente}', function (App\Models\Expediente $expediente) {
        return view('test-ia', compact('expediente'));
    });
    Route::get('/documentos/{documento}', [\App\Http\Controllers\DocumentController::class, 'show'])->name('documentos.show');
    Route::get('/bitacora', \App\Livewire\Audit\Index::class)->name('audit.index');
    
    Route::get('/mensajes', \App\Livewire\Mensajes\Index::class)->name('mensajes.index');
    Route::get('/expedientes/{expediente}/asignaciones', \App\Livewire\Expedientes\ManageAssignments::class)->name('expedientes.assignments')->middleware('can:manage users');

    // AI Global Assistant
    Route::get('/asistente-ia', \App\Livewire\AiGlobalAssistant::class)->name('ai.assistant');

    // Monitor DOF (Diario Oficial)
    Route::get('/dof', \App\Livewire\Dof\Index::class)->name('dof.index');
    Route::get('/jurisprudencia', \App\Livewire\Sjf\Index::class)->name('sjf.index');
    
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
    Route::get('/admin/announcements', \App\Livewire\Admin\Announcements::class)->name('admin.announcements')->middleware('can:manage tenants');
    
    // Legal Documents
    Route::get('/admin/legal-documents', \App\Livewire\Admin\LegalDocuments\Index::class)->name('admin.legal-documents.index')->middleware('can:manage settings');
    Route::get('/admin/legal-documents/create', \App\Livewire\Admin\LegalDocuments\Form::class)->name('admin.legal-documents.create')->middleware('can:manage settings');
    Route::get('/admin/legal-documents/{legalDocument}/edit', \App\Livewire\Admin\LegalDocuments\Form::class)->name('admin.legal-documents.edit')->middleware('can:manage settings');

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
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
