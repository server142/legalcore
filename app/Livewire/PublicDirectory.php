<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DirectoryProfile;

class PublicDirectory extends Component
{
    use WithPagination;

    public $search = '';
    public $state = '';
    public $specialty = '';
    public $city = '';

    // List of Mexican States for the dropdown
    public $statesList = [
        'Aguascalientes', 'Baja California', 'Baja California Sur', 'Campeche', 'Chiapas', 
        'Chihuahua', 'Ciudad de México', 'Coahuila', 'Colima', 'Durango', 'Guanajuato', 
        'Guerrero', 'Hidalgo', 'Jalisco', 'México', 'Michoacán', 'Morelos', 'Nayarit', 
        'Nuevo León', 'Oaxaca', 'Puebla', 'Querétaro', 'Quintana Roo', 'San Luis Potosí', 
        'Sinaloa', 'Sonora', 'Tabasco', 'Tamaulipas', 'Tlaxcala', 'Veracruz', 'Yucatán', 'Zacatecas'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingState()
    {
        $this->resetPage();
    }

    public function updatingCity()
    {
        $this->resetPage();
    }

    public function render()
    {
        $profiles = DirectoryProfile::with('user')
            ->where('is_public', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('headline', 'like', '%' . $this->search . '%')
                      ->orWhere('bio', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($u) {
                          $u->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->state, function ($query) {
                $query->where('state', 'like', '%' . $this->state . '%');
            })
            ->when($this->city, function ($query) {
                $query->where('city', 'like', '%' . $this->city . '%');
            })
            ->when($this->specialty, function ($query) {
                 $query->whereJsonContains('specialties', $this->specialty);
            })
            ->orderByDesc('is_verified') // Verified first
            ->latest()
            ->paginate(12);

        // Get unique specialties for filter
        $allSpecialties = DirectoryProfile::where('is_public', true)
            ->pluck('specialties')
            ->flatten()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        return view('livewire.public-directory', [
            'profiles' => $profiles,
            'allSpecialties' => $allSpecialties
        ])->layout('layouts.public'); 
    }
}
