<?php
use App\Models\User;
use App\Models\Evento;
use Carbon\Carbon;

$user = User::first();
if (!$user) {
    echo "No user found\n";
    exit;
}

echo "Creating test event for user: " . $user->email . "\n";

$evento = Evento::create([
    'tenant_id' => $user->tenant_id,
    'user_id' => $user->id,
    'titulo' => 'Test Debug Event ' . now()->format('H:i:s'),
    'descripcion' => 'Debugging google calendar sync',
    'start_time' => Carbon::now()->addHour(),
    'end_time' => Carbon::now()->addHours(2),
    'tipo' => 'cita',
]);

echo "Event created in DB with ID: " . $evento->id . "\n";
echo "Google Event ID: " . ($evento->google_event_id ?? 'NULL') . "\n";
