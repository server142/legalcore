<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;

use App\Models\Expediente;
use App\Models\Cliente;
use App\Models\Actuacion;

class TenantAdmin extends Component
{
    public $activeExpedientes;
    public $upcomingDeadlines;
    public $totalClientes;
    public $recentExpedientes;
    public $urgentTerminos;
    public $eventos;
    public $sjfCount;
    public $dofCount;
    
    // Financial Stats
    public $totalCobrado;
    public $pendienteCobro;
    public $facturasMes;
    public $monthlyIncome;
    public $lastMonthIncome;
    public $projectedIncome;
    public $incomeHistory = [];
    public $incomeByMateria = ['labels' => [], 'values' => []];
    public $recentPayments = [];
    public $topDebtors = [];
    public $activityHistory = [];

    public function mount()
    {
        $user = auth()->user();
        $isAbogado = $user->hasRole('abogado') && !$user->can('view all expedientes');

        $expedienteQuery = Expediente::query();
        $actuacionQuery = Actuacion::where('es_plazo', true)->where('estado', 'pendiente');

        if ($isAbogado) {
            $expedienteQuery->where(function($q) use ($user) {
                $q->where('abogado_responsable_id', $user->id)
                  ->orWhereHas('assignedUsers', function($q2) use ($user) {
                      $q2->where('users.id', $user->id);
                  });
            });
        }

        // For terminos, check the specific permission
        if ($user->hasRole('abogado') && !$user->can('view all terminos')) {
            $actuacionQuery->whereHas('expediente', function($q) use ($user) {
                $q->where('abogado_responsable_id', $user->id)
                  ->orWhereHas('assignedUsers', function($q2) use ($user) {
                      $q2->where('users.id', $user->id);
                  });
            });
        }

        $this->activeExpedientes = (clone $expedienteQuery)->where('estado_procesal', '!=', 'Archivo')->count();
        
        $this->upcomingDeadlines = (clone $actuacionQuery)
            ->where('fecha_vencimiento', '>=', now())
            ->where('fecha_vencimiento', '<=', now()->addDays(7))
            ->count();

        $this->totalClientes = Cliente::count(); 
        $this->recentExpedientes = $expedienteQuery->with('estadoProcesal')->latest()->take(5)->get();
        
        $this->urgentTerminos = (clone $actuacionQuery)
            ->orderBy('fecha_vencimiento', 'asc')
            ->take(5)
            ->get();

        // ---------------------------------------------------------
        // Activity History (Last 6 Months) - Real Data
        // ---------------------------------------------------------
        $this->activityHistory = [];
        $maxVal = 1;
        
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            
            $casosCount = (clone $expedienteQuery)
                ->whereMonth('created_at', $monthDate->month)
                ->whereYear('created_at', $monthDate->year)
                ->count();
                
            $docsCount = \App\Models\Documento::whereMonth('created_at', $monthDate->month)
                ->whereYear('created_at', $monthDate->year)
                ->whereIn('expediente_id', function($query) use ($isAbogado, $user) {
                    // Use Eloquent Model instead of plain table to support orWhereHas
                    $expQuery = \App\Models\Expediente::select('id');
                    
                    if ($isAbogado) {
                        $expQuery->where(function($q) use ($user) {
                            $q->where('abogado_responsable_id', $user->id)
                              ->orWhereHas('assignedUsers', function($q2) use ($user) {
                                  $q2->where('users.id', $user->id);
                              });
                        });
                    }
                    
                    return $query->from($expQuery);
                })
                ->count();

            if ($casosCount > $maxVal) $maxVal = $casosCount;
            if ($docsCount > $maxVal) $maxVal = $docsCount;

            $this->activityHistory[] = [
                'label' => $monthDate->translatedFormat('M'),
                'casos' => $casosCount,
                'docs' => $docsCount,
                'casos_h' => 0, // Placeholder for normalization
                'docs_h' => 0,
            ];
        }

        // Normalize heights for display (min 5% for visibility if not zero)
        foreach ($this->activityHistory as &$item) {
            $item['casos_h'] = $item['casos'] > 0 ? max(($item['casos'] / $maxVal) * 100, 5) : 2;
            $item['docs_h'] = $item['docs'] > 0 ? max(($item['docs'] / $maxVal) * 100, 5) : 2;
        }

        // Agenda
        $agendaQuery = \App\Models\Evento::with('user');
        if ($user->hasRole('abogado') && !$user->can('view all expedientes')) {
            $agendaQuery->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('expediente', function($qe) use ($user) {
                      $qe->where('abogado_responsable_id', $user->id)
                         ->orWhereHas('assignedUsers', function($qu) use ($user) {
                             $qu->where('users.id', $user->id);
                         });
                  })
                  ->orWhereHas('invitedUsers', function($qi) use ($user) {
                      $qi->where('users.id', $user->id);
                  });
            });
        }
        $this->eventos = (clone $agendaQuery)->where('start_time', '>=', now()->startOfDay())
            ->where('start_time', '<=', now()->addDays(7)->endOfDay())
            ->orderBy('start_time')
            ->take(10)
            ->get();
        
        // Financial Stats
        if ($user->can('manage billing')) {
            try {
                // KPIs
                $this->totalCobrado = \App\Models\Factura::where('estado', 'pagada')->sum('total');
                $this->pendienteCobro = \App\Models\Factura::where('estado', 'pendiente')->sum('total');
                $this->facturasMes = \App\Models\Factura::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();

                // Advanced Logic
                $this->monthlyIncome = \App\Models\Factura::where('estado', 'pagada')
                    ->whereMonth('fecha_pago', now()->month)
                    ->whereYear('fecha_pago', now()->year)
                    ->sum('total');

                $this->lastMonthIncome = \App\Models\Factura::where('estado', 'pagada')
                    ->whereMonth('fecha_pago', now()->subMonth()->month)
                    ->whereYear('fecha_pago', now()->subMonth()->year)
                    ->sum('total');

                $pendingDueThisMonth = \App\Models\Factura::where('estado', 'pendiente')
                    ->whereMonth('fecha_vencimiento', now()->month)
                    ->whereYear('fecha_vencimiento', now()->year)
                    ->sum('total');
                $this->projectedIncome = $this->monthlyIncome + $pendingDueThisMonth;

                // History
                $this->incomeHistory = [];
                for ($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $amount = \App\Models\Factura::where('estado', 'pagada')
                        ->whereMonth('fecha_pago', $date->month)
                        ->whereYear('fecha_pago', $date->year)
                        ->sum('total');
                    $this->incomeHistory[] = [
                        'label' => $date->translatedFormat('M'), 
                        'value' => $amount
                    ];
                }

                // By Materia
                $facturasYear = \App\Models\Factura::where('estado', 'pagada')
                    ->whereYear('fecha_pago', now()->year)
                    ->with('expediente:id,materia')
                    ->get();
                
                $byMateria = $facturasYear->groupBy('expediente.materia')->map(function ($row) {
                    return $row->sum('total');
                })->sortDesc()->take(5);

                $this->incomeByMateria = [
                    'labels' => $byMateria->keys()->toArray(),
                    'values' => $byMateria->values()->toArray(),
                ];

                // Tables
                $this->recentPayments = \App\Models\Factura::where('estado', 'pagada')
                    ->with(['expediente', 'cliente'])
                    ->latest('fecha_pago')
                    ->take(5)
                    ->get();
                
                $this->topDebtors = \App\Models\Expediente::where('saldo_pendiente', '>', 0)
                    ->orderBy('saldo_pendiente', 'desc')
                    ->take(5)
                    ->get();

            } catch (\Throwable $e) {
                $this->resetFinancials();
                \Illuminate\Support\Facades\Log::warning('TenantAdmin Dashboard Financial Error: ' . $e->getMessage());
            }
        } else {
            $this->resetFinancials();
        }

        $this->sjfCount = \App\Models\SjfPublication::count();
        $this->dofCount = \App\Models\DofPublication::count();
    }

    private function resetFinancials()
    {
        $this->totalCobrado = 0;
        $this->pendienteCobro = 0;
        $this->facturasMes = 0;
        $this->monthlyIncome = 0;
        $this->lastMonthIncome = 0;
        $this->projectedIncome = 0;
        $this->incomeHistory = [];
        $this->incomeByMateria = ['labels' => [], 'values' => []];
        $this->recentPayments = [];
        $this->topDebtors = [];
    }

    public function render()
    {
        return view('livewire.dashboards.tenant-admin');
    }
}
