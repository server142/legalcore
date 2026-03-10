<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Carbon\Carbon;

class DebugDates extends Command
{
    protected $signature = 'debug:dates';
    protected $description = 'Debug date calculations for trial expiration';


    public function handle()
    {
        $this->info("--- DEBUG DATE CALCULATION ALL ---");
        $this->info("Current Time: " . Carbon::now()->toDateTimeString());

        $tenants = \DB::table('tenants')->get();

        foreach ($tenants as $tenant) {
            $this->info("Tenant: {$tenant->name} (ID: {$tenant->id})");
            $this->info("  Plan: {$tenant->plan}");
            $date = $tenant->trial_ends_at;

            if ($date) {
                $carbonDate = Carbon::parse($date);
                $this->info("  Trial Ends At (Raw): " . $date);
                $this->info("  Trial Ends At (Carbon): " . $carbonDate->toDateTimeString());
                
                $diff = Carbon::now()->diffInDays($carbonDate, false);
                $floatDiff = Carbon::now()->floatDiffInDays($carbonDate, false);
                
                $this->info("  diffInDays: $diff");
                $this->info("  floatDiffInDays: $floatDiff");
                
                // Emulate logic from App Layout
                $daysLeftCeil = ceil($floatDiff);
                $this->info("  App Logic (ceil float): $daysLeftCeil");
            } else {
                 $this->info("  Trial Ends At: NULL");
            }
            $this->line("----------------");
        }
    }
}
