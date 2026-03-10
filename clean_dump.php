<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\Plan::all() as $p) {
    echo sprintf("ID: %d | Slug: %-20s | Name: %s\n", $p->id, $p->slug, $p->name);
}
echo "\n";
foreach (App\Models\Tenant::all() as $t) {
    echo sprintf("Tenant ID: %d | Plan: %-20s\n", $t->id, $t->plan);
}
