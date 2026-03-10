<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t = App\Models\Tenant::with('users')->find(11);
if ($t) {
    echo "Tenant ID: 11 | Plan: {$t->plan}\n";
    foreach ($t->users as $u) {
        echo " - User: {$u->email}\n";
    }
} else {
    echo "Tenant 11 not found.\n";
}
