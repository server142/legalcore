<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\Payment;
use Livewire\WithPagination;

class IncomeReport extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $totalIncome = 0;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->totalIncome = Payment::whereBetween('payment_date', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'startDate' || $propertyName === 'endDate') {
            $this->calculateTotal();
            $this->resetPage();
        }
    }

    public function render()
    {
        $payments = Payment::with(['tenant', 'plan'])
            ->whereBetween('payment_date', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('status', 'completed')
            ->latest('payment_date')
            ->paginate(20);

        return view('livewire.admin.reports.income-report', [
            'payments' => $payments
        ])->layout('layouts.app');
    }
}
