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
        $profiles = DirectoryProfile::select('directory_profiles.*')
            ->with(['user', 'user.tenant'])
            ->join('users', 'directory_profiles.user_id', '=', 'users.id')
            ->join('tenants', 'users.tenant_id', '=', 'tenants.id')
            ->where('directory_profiles.is_public', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('directory_profiles.headline', 'like', '%' . $this->search . '%')
                      ->orWhere('directory_profiles.bio', 'like', '%' . $this->search . '%')
                      ->orWhere('users.name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->state, function ($query) {
                $query->where('directory_profiles.state', 'like', '%' . $this->state . '%');
            })
            ->when($this->city, function ($query) {
                $query->where('directory_profiles.city', 'like', '%' . $this->city . '%');
            })
            ->when($this->specialty, function ($query) {
                 $query->whereJsonContains('directory_profiles.specialties', $this->specialty);
            })
            // 1. MAX PRIORITY: PRO plans and Full Diogenes System Users
            // 2. MID PRIORITY: Basic Directory Plans
            // 3. LOW PRIORITY: Free Directory Plans
            ->orderByRaw("
                CASE 
                    WHEN tenants.plan LIKE '%pro%' OR tenants.plan NOT LIKE 'directory-%' THEN 3
                    WHEN tenants.plan LIKE '%basic%' THEN 2
                    ELSE 1 
                END DESC
            ")
            ->orderByDesc('directory_profiles.is_verified') // Verified first among same tier
            ->latest('directory_profiles.created_at')
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
