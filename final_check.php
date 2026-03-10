<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$profiles = App\Models\DirectoryProfile::with('user.tenant')->get();
foreach ($profiles as $p) {
    echo "User: " . $p->user->email . " | Plan: " . ($p->user->tenant->plan ?? 'NULL') . "\n";
}
