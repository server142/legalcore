<?php

use App\Models\Tenant;

$tenant = Tenant::find(11); // ID del tenant del usuario reportado
if ($tenant) {
    if ($tenant->plan !== 'directory-free') {
        $tenant->update(['plan' => 'directory-free', 'subscription_status' => 'active']);
        echo "Tenant {$tenant->name} updated to plan directory-free.\n";
    } else {
        echo "Tenant already on directory-free.\n";
    }
} else {
    echo "Tenant 11 not found.\n";
}
