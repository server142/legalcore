<?php

namespace App\Livewire\Admin\Plans;

use App\Models\Plan;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function delete(Plan $plan)
    {
        $plan->delete();
        session()->flash('message', 'Plan eliminado correctamente.');
    }

    public function toggleStatus(Plan $plan)
    {
        $plan->is_active = !$plan->is_active;
        $plan->save();
    }

    public function render()
    {
        $plans = Plan::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('price', 'asc')
            ->paginate(10);

        return view('livewire.admin.plans.index', [
            'plans' => $plans
        ])->layout('layouts.app');
    }
}
