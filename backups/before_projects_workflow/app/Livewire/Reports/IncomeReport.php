<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Factura;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IncomeReport extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $totalIncome = 0;
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

        return Factura::query()
            ->where('estado', 'pagada')
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('fecha_pago', [$from, $to])
                    ->orWhere(function ($q2) use ($from, $to) {
                        $q2->whereNull('fecha_pago')
                            ->whereBetween(DB::raw('COALESCE(fecha_emision, created_at)'), [$from, $to]);
                    });
            });
    }

    public function consultar(): void
    {
        $this->hasSearched = true;
        $this->resetPage();
        $this->totalIncome = (clone $this->baseQuery())->sum('total');
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
        $facturas = $this->baseQuery()
            ->with('cliente')
            ->orderByRaw('COALESCE(fecha_pago, fecha_emision, created_at) desc')
            ->paginate(20);

        return view('livewire.reports.income-report', [
            'facturas' => $facturas,
        ])->layout('layouts.app');
    }
}
