<?php

use App\Models\Tenant;
use Carbon\Carbon;

// Force Mexico City Timezone for testing
date_default_timezone_set('America/Mexico_City');

echo "--- DEBUG DATE CALCULATION ---\n";
echo "Current Time (PHP default): " . date('Y-m-d H:i:s') . "\n";
echo "Current Time (Carbon::now): " . Carbon::now()->toDateTimeString() . "\n";

$tenant = Tenant::where('plan', 'trial')->where('trial_ends_at', '>', now())->first();

if (!$tenant) {
    echo "No matching trial tenant found.\n";
    exit;
}

echo "Tenant ID: " . $tenant->id . "\n";
echo "Tenant Trial Ends At (Raw DB): " . $tenant->getRawOriginal('trial_ends_at') . "\n";
echo "Tenant Trial Ends At (Carbon): " . $tenant->trial_ends_at->toDateTimeString() . "\n";

$daysLeft = Carbon::now()->diffInDays($tenant->trial_ends_at, false);
echo "Carbon diffInDays (false): " . $daysLeft . "\n";

$daysLeftFloat = Carbon::now()->floatDiffInDays($tenant->trial_ends_at, false);
echo "Carbon floatDiffInDays: " . $daysLeftFloat . "\n";

$manualDiff = (strtotime($tenant->trial_ends_at) - time()) / 86400;
echo "Manual Calculation (seconds / 86400): " . $manualDiff . "\n";

echo "Tenant Method daysLeftInTrial(): " . $tenant->daysLeftInTrial() . "\n";
