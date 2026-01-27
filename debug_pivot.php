<?php

use App\Models\Evento;
use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Debugging Evento User Pivot ---\n";

// 1. Check if table exists and has data
try {
    $count = DB::table('evento_user')->count();
    echo "Total rows in 'evento_user': $count\n";
    
    $rows = DB::table('evento_user')->orderBy('id', 'desc')->take(5)->get();
    foreach ($rows as $row) {
        echo "Row: EventoID={$row->evento_id}, UserID={$row->user_id}\n";
    }
} catch (\Exception $e) {
    echo "Error accessing table: " . $e->getMessage() . "\n";
}

echo "\n--- Checking Latest Events ---\n";
$eventos = Evento::with(['invitedUsers'])->latest()->take(3)->get();
foreach ($eventos as $evento) {
    echo "Evento [{$evento->id}] '{$evento->titulo}'\n";
    echo "  - Invitados count: " . $evento->invitedUsers->count() . "\n";
    foreach ($evento->invitedUsers as $u) {
        echo "    - {$u->name} ({$u->id})\n";
    }
}

echo "\n--- Attempting Manual Sync Test ---\n";
// Create a dummy event and try to sync a user
try {
    DB::beginTransaction();
    $user = User::first();
    if ($user) {
        $evento = Evento::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'titulo' => 'Debug Sync Test',
            'start_time' => now(),
            'end_time' => now()->addHour(),
            'tipo' => 'cita',
            'descripcion' => 'Test'
        ]);
        echo "Created Event ID: {$evento->id}\n";
        
        // Try to attach the same user (just for test) or another if exists
        $otherUser = User::where('id', '!=', $user->id)->first();
        if ($otherUser) {
            echo "Attaching User ID: {$otherUser->id}\n";
            $evento->invitedUsers()->sync([$otherUser->id]);
            
            // Verify immediately
            $check = DB::table('evento_user')->where('evento_id', $evento->id)->count();
            echo "Rows in pivot for this event: $check\n";
        } else {
            echo "No other user found to attach.\n";
        }
    }
    DB::rollBack();
    echo "Test rolled back.\n";
} catch (\Exception $e) {
    echo "Test failed: " . $e->getMessage() . "\n";
}
