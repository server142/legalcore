<?php

use App\Models\User;
use App\Models\Tenant;

$user = User::latest()->first();
$tenant = Tenant::latest()->first();

if ($user && $tenant) {
    echo "User: {$user->name} (ID: {$user->id})\n";
    echo "Current Tenant ID: " . ($user->tenant_id ?? 'NULL') . "\n";
    echo "Latest Tenant: {$tenant->name} (ID: {$tenant->id}, Plan: {$tenant->plan})\n";

    if (!$user->tenant_id) {
        $user->tenant_id = $tenant->id;
        $user->save();
        echo "FIXED: User assigned to tenant {$tenant->id}.\n";
    } else {
        echo "User already has a tenant.\n";
    }
} else {
    echo "User or Tenant not found.\n";
}
