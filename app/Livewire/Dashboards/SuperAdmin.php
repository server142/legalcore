<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;

use App\Models\Tenant;
use App\Models\User;

class SuperAdmin extends Component
{
    public $totalTenants;
    public $activeTenants;
    public $totalUsers;
    public $tenants;
    public $monthlyIncome;

    public $domainDaysLeft;
    public $domainHoursLeft;
    public $domainIsExpired = false;
    public $vpsCost;
    public $aiBudget;
    public $aiCurrentSpend;
    public $aiTenantUsage;
    public $aiDailySpend;

    public function mount()
    {
        $this->totalTenants = Tenant::count();
        $this->activeTenants = Tenant::where('status', 'active')->count();
        $this->totalUsers = User::count();
        $this->tenants = Tenant::latest()->take(10)->get();
        
        // Calcular ingresos del mes actual
        try {
            $this->monthlyIncome = \App\Models\Payment::whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->where('status', 'completed')
                ->sum('amount');
        } catch (\Throwable $e) {
            $this->monthlyIncome = 0;
            \Illuminate\Support\Facades\Log::warning('SuperAdmin Dashboard: Error al calcular ingresos. ' . $e->getMessage());
        }

        // --- Infrastructure & AI Monitoring ---
        
        $settings = \Illuminate\Support\Facades\DB::table('global_settings')
            ->whereIn('key', ['infrastructure_domain_expiry', 'infrastructure_vps_cost', 'infrastructure_ai_budget'])
            ->pluck('value', 'key');

        // Domain Expiry
        $expiryDate = $settings['infrastructure_domain_expiry'] ?? null;
        if ($expiryDate) {
            try {
                $expiry = \Carbon\Carbon::parse($expiryDate);
                $diff = now()->diff($expiry);
                
                $this->domainIsExpired = $diff->invert;
                $this->domainDaysLeft = $this->domainIsExpired ? 0 : $diff->days;
                $this->domainHoursLeft = $this->domainIsExpired ? 0 : $diff->h;
            } catch (\Exception $e) {
                $this->domainDaysLeft = null;
                $this->domainHoursLeft = null;
            }
        } else {
            $this->domainDaysLeft = null;
            $this->domainHoursLeft = null;
        }

        $this->vpsCost = floatval($settings['infrastructure_vps_cost'] ?? 0);
        $this->aiBudget = floatval($settings['infrastructure_ai_budget'] ?? 1);

        // AI Consumption (This Month)
        try {
            $this->aiCurrentSpend = \App\Models\AiUsageLog::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('cost');

            // Usage Per Tenant
            $this->aiTenantUsage = \App\Models\AiUsageLog::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->selectRaw('tenant_id, sum(cost) as total_cost, count(*) as requests')
                ->groupBy('tenant_id')
                ->with('tenant')
                ->orderByDesc('total_cost')
                ->take(5)
                ->get();

            // Daily History (Last 30 days) for the Graph
            $this->aiDailySpend = \App\Models\AiUsageLog::where('created_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(created_at) as date, sum(cost) as total_cost')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

        } catch (\Throwable $e) {
            $this->aiCurrentSpend = 0;
            $this->aiTenantUsage = collect();
            $this->aiDailySpend = collect();
        }
    }

    public function render()
    {
        return view('livewire.dashboards.super-admin');
    }
}
