<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Carbon\Carbon;

class DeepDebugTenant extends Command
{
    protected $signature = 'debug:deep-tenant';
    protected $description = 'Deep inspection of tenant dates';

    public function handle()
    {
        $tenant = Tenant::first();

        if (!$tenant) {
            $this->error("No trial tenant found.");
            return;
        }

        $this->info("=== TENANT DEEP DEBUG ===");
        $this->info("ID: " . $tenant->id);
        $this->info("Name: " . $tenant->name);
        $this->info("Plan: " . $tenant->plan);
        $this->info("Status: " . $tenant->status);
        $this->info("Is Active: " . ($tenant->is_active ? 'YES' : 'NO'));
        
        $this->info("\n--- DATES (RAW vs ATTRIBUTE) ---");
        $this->info("Trial Ends At (Raw): " . $tenant->getRawOriginal('trial_ends_at'));
        $this->info("Trial Ends At (Attr): " . $tenant->trial_ends_at);
        
        $this->info("Subscription Ends At (Raw): " . $tenant->getRawOriginal('subscription_ends_at'));
        $this->info("Subscription Ends At (Attr): " . $tenant->subscription_ends_at);

        $this->info("\n--- CALCULATIONS ---");
        $now = Carbon::now();
        $this->info("Now: " . $now->toDateTimeString());
        
        if ($tenant->trial_ends_at) {
            $diff = $now->diffInDays($tenant->trial_ends_at, false);
            $floatDiff = $now->floatDiffInDays($tenant->trial_ends_at, false);
            $secondsDiff = $now->diffInSeconds($tenant->trial_ends_at, false);
            
            $this->info("Diff In Days (int): " . $diff);
            $this->info("Diff In Days (float): " . $floatDiff);
            $this->info("Diff In Seconds: " . $secondsDiff);
            $this->info("Manual Days (seconds/86400): " . ($secondsDiff / 86400));
        }

        $this->info("\n--- METHOD RESULTS ---");
        $this->info("isOnTrial(): " . ($tenant->isOnTrial() ? 'YES' : 'NO'));
        $this->info("daysLeftInTrial(): " . $tenant->daysLeftInTrial());
    }
}
