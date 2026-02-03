<?php

namespace App\Livewire\Admin\Plans;

use App\Models\Plan;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    
    // Modal state
    public $showFeaturesModal = false;
    public $editingPlanId = null;
    public $features = [];
    public $newFeature = '';

    public function openFeaturesModal(Plan $plan)
    {
        $this->editingPlanId = $plan->id;
        
        if (is_string($plan->features)) {
            $this->features = json_decode($plan->features, true) ?? [];
        } else {
            $this->features = $plan->features ?? [];
        }
        
        $this->showFeaturesModal = true;
    }

    public function addFeature()
    {
        if (trim($this->newFeature) !== '') {
            $this->features[] = $this->newFeature;
            $this->newFeature = '';
        }
    }

    public function removeFeature($index)
    {
        unset($this->features[$index]);
        $this->features = array_values($this->features);
    }

    public function saveFeatures()
    {
        $plan = Plan::find($this->editingPlanId);
        if ($plan) {
            $plan->update(['features' => $this->features]);
            session()->flash('message', 'Características actualizadas correctamente.');
        }
        
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showFeaturesModal = false;
        $this->editingPlanId = null;
        $this->features = [];
        $this->newFeature = '';
    }

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
