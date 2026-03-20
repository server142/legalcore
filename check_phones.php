<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::where('tenant_id', 5)->get();
foreach($users as $u) {
    echo $u->name . ' (' . $u->id . '): ' . $u->telefono . "\n";
}
