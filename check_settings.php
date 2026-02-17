<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = DB::table('global_settings')->get();
foreach($settings as $s) {
    echo "{$s->key}: {$s->value}\n";
}
