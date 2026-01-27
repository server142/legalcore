<?php

use App\Models\Evento;
use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Debugging Last 5 Events ---\n";

$eventos = Evento::with(['user', 'invitedUsers', 'expediente'])->latest()->take(5)->get();

foreach ($eventos as $evento) {
    echo "ID: {$evento->id} | Título: {$evento->titulo} | Creador: {$evento->user->name} ({$evento->user->id})\n";
    
    echo "  -> Invitados (Pivot table count): " . $evento->invitedUsers->count() . "\n";
    foreach ($evento->invitedUsers as $invited) {
        echo "     - {$invited->name} (ID: {$invited->id})\n";
    }
    
    if ($evento->expediente) {
        echo "  -> Expediente: {$evento->expediente->numero}\n";
    } else {
        echo "  -> Sin Expediente\n";
    }
    echo "---------------------------------------------------\n";
}

echo "\n--- Checking Current User (Auth) ---\n";
// We can't easily check auth() in CLI without login, but we can check a specific user if we knew their ID.
// Let's just list all users to see if IDs match what we expect.
$users = User::all(['id', 'name', 'email', 'tenant_id']);
foreach ($users as $u) {
    echo "User: {$u->name} | ID: {$u->id} | Tenant: {$u->tenant_id}\n";
}
