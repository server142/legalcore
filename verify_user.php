<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$u = User::where('email', 'mail@gmail.com')->first();
if ($u && $u->tenant) {
    echo "User: " . $u->email . "\n";
    echo "Tenant Plan: " . $u->tenant->plan . "\n";
    echo "Is Dir? " . (str_starts_with($u->tenant->plan, 'directory-') ? 'YES' : 'NO') . "\n";
} else {
    echo "User not found or no tenant.\n";
}
