<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Plan;
use App\Models\Tenant;

echo "--- ALL PLANS ---\n";
foreach (Plan::all() as $p) {
    echo "Slug: {$p->slug} | Name: {$p->name}\n";
}

echo "\n--- ALL TENANTS & PLANS ---\n";
foreach (Tenant::all() as $t) {
    echo "Tenant ID: {$t->id} | Plan Field: {$t->plan}\n";
}
