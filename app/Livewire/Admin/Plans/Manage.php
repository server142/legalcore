<?php

namespace App\Livewire\Admin\Plans;

use App\Models\Plan;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Manage extends Component
{
    public ?Plan $plan = null;
    
    // Form fields
    public $name = '';
    public $slug = '';
    public $stripe_price_id = '';
    public $price = 0.00;
    public $duration_in_days = 30;
    public $max_admin_users = 1;
    public $max_lawyer_users = null;
    public $is_active = true;
    public $features = [];
    
    // Feature helper
    public $newFeature = '';

    public function mount(Plan $plan = null)
    {
        if ($plan && $plan->exists) {
            $this->plan = $plan;
            $this->name = $plan->name;
            $this->slug = $plan->slug;
            $this->stripe_price_id = $plan->stripe_price_id;
            $this->price = $plan->price;
            $this->duration_in_days = $plan->duration_in_days;
            $this->max_admin_users = $plan->max_admin_users;
            $this->max_lawyer_users = $plan->max_lawyer_users;
            $this->is_active = $plan->is_active;
            
            // Asegurar que features sea un array
            if (is_string($plan->features)) {
                $this->features = json_decode($plan->features, true) ?? [];
            } else {
                $this->features = $plan->features ?? [];
            }
        }
    }

    public function updatedName($value)
    {
        if (!$this->plan) {
            $this->slug = Str::slug($value);
        }
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

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('plans')->ignore($this->plan)],
            'stripe_price_id' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'max_admin_users' => 'required|integer|min:1',
            'max_lawyer_users' => 'nullable|integer|min:0',
            'features' => 'array',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'stripe_price_id' => $this->stripe_price_id,
            'price' => $this->price,
            'duration_in_days' => $this->duration_in_days,
            'max_admin_users' => $this->max_admin_users,
            'max_lawyer_users' => $this->max_lawyer_users === '' ? null : $this->max_lawyer_users,
            'is_active' => $this->is_active,
            'features' => $this->features,
        ];

        if ($this->plan) {
            $this->plan->update($data);
            session()->flash('message', 'Plan actualizado correctamente.');
        } else {
            Plan::create($data);
            session()->flash('message', 'Plan creado correctamente.');
        }

        return redirect()->route('admin.plans.index');
    }

    public function render()
    {
        return view('livewire.admin.plans.manage')->layout('layouts.app');
    }
}
