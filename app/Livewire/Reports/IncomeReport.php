<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Factura;
use App\Models\ExpedientePago;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncomeReport extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $totalIncome = 0;
    public $totalFacturas = 0;
    public $totalAnticipos = 0;
    public $hasSearched = false;

    public function mount(): void
    {
        if (!auth()->user()->can('manage billing')) {
            abort(403);
        }

        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->consultar();
    }

    private function rangeStart(): string
    {
        return $this->startDate . ' 00:00:00';
    }

    private function rangeEnd(): string
    {
        return $this->endDate . ' 23:59:59';
    }

    private function baseQuery()
    {
        $from = $this->rangeStart();
        $to = $this->rangeEnd();

        // Query para facturas
        $facturasQuery = Factura::query()
            ->where('estado', 'pagada')
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('fecha_pago', [$from, $to])
                    ->orWhere(function ($q2) use ($from, $to) {
                        $q2->whereNull('fecha_pago')
                            ->whereBetween(DB::raw('COALESCE(fecha_emision, created_at)'), [$from, $to]);
                    });
            });

        // Query para anticipos de expedientes
        $anticiposQuery = ExpedientePago::query()
            ->where('tipo_pago', 'anticipo')
            ->whereBetween('fecha_pago', [$from, $to]);

        // Combinar ambas consultas usando union
        return $facturasQuery->union($anticiposQuery);
    }

    public function consultar(): void
    {
        $this->hasSearched = true;
        $this->resetPage();
        
        $from = $this->rangeStart();
        $to = $this->rangeEnd();
        
        // Calcular totales por separado (TODOS los tiempos, del tenant actual)
        $this->totalFacturas = Factura::where('tenant_id', auth()->user()->tenant_id)
            ->where('estado', 'pagada')
            ->sum('total');
        
        $this->totalAnticipos = ExpedientePago::where('tenant_id', auth()->user()->tenant_id)
            ->sum('monto');
        
        $this->totalIncome = $this->totalFacturas + $this->totalAnticipos;
    }

    public function exportarCsv(): StreamedResponse
    {
        $user = auth()->user();
        if (!$user || !$user->can('manage billing')) {
            abort(403);
        }

        $from = $this->rangeStart();
        $to = $this->rangeEnd();
        $filename = 'reporte-ingresos-' . $this->startDate . '-a-' . $this->endDate . '.csv';

        $query = (clone $this->baseQuery())
            ->with('cliente')
            ->orderByRaw('COALESCE(fecha_pago, fecha_emision, created_at) asc');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['FechaPago', 'Cliente', 'Concepto', 'Subtotal', 'IVA', 'Total', 'Moneda', 'FacturaId']);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $factura) {
                    $fecha = $factura->fecha_pago ?? $factura->fecha_emision ?? $factura->created_at;
                    $concepto = data_get($factura->conceptos, '0.descripcion') ?? '';

                    fputcsv($out, [
                        optional($fecha)->format('Y-m-d H:i:s') ?? '',
                        $factura->cliente->nombre ?? '',
                        $concepto,
                        $factura->subtotal,
                        $factura->iva,
                        $factura->total,
                        $factura->moneda,
                        $factura->id,
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function render()
    {
        // Obtener facturas pagadas (del tenant actual)
        $facturas = Factura::where('tenant_id', auth()->user()->tenant_id)
            ->where('estado', 'pagada')
            ->with('cliente')
            ->orderByRaw('COALESCE(fecha_pago, fecha_emision, created_at) desc')
            ->get();
            
        // Obtener anticipos (del tenant actual, todos los tipos)
        $anticipos = ExpedientePago::where('tenant_id', auth()->user()->tenant_id)
            ->with('expediente.cliente')
            ->orderBy('fecha_pago', 'desc')
            ->get();
            
        // Combinar y ordenar todos los ingresos
        $ingresos = collect();
        
        foreach ($facturas as $factura) {
            $ingresos->push((object)[
                'tipo' => 'factura',
                'fecha' => $factura->fecha_pago ?? $factura->fecha_emision ?? $factura->created_at,
                'cliente' => $factura->cliente->nombre ?? '',
                'concepto' => data_get($factura->conceptos, '0.descripcion') ?? 'Factura',
                'monto' => $factura->total,
                'moneda' => $factura->moneda,
                'id' => $factura->id,
                'referencia' => 'FAC-' . $factura->id,
            ]);
        }
        
        foreach ($anticipos as $anticipo) {
            $ingresos->push((object)[
                'tipo' => 'pago',
                'fecha' => $anticipo->fecha_pago,
                'cliente' => $anticipo->expediente->cliente->nombre ?? '',
                'concepto' => ($anticipo->tipo_pago == 'anticipo' ? 'Anticipo' : 'Parcial') . ' - ' . $anticipo->expediente->numero,
                'monto' => $anticipo->monto,
                'moneda' => 'MXN',
                'id' => $anticipo->id,
                'referencia' => $anticipo->referencia ?? 'PAG-' . $anticipo->id,
            ]);
        }
        
        // Ordenar por fecha descendente
        $ingresos = $ingresos->sortByDesc('fecha');
        
        // Paginación manual
        $page = request()->get('page', 1);
        $perPage = 20;
        $total = $ingresos->count();
        $ingresosPaginados = $ingresos->forPage($page, $perPage);

        return view('livewire.reports.income-report', [
            'ingresos' => $ingresosPaginados,
            'total' => $total,
        ])->layout('layouts.app');
    }
}
