<?php

namespace App\Livewire\Admin\Directory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DirectoryProfile;
use App\Models\DirectoryAnalytic;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $confirmingDeletion = false;
    public $selectedProfileId;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleVisibility($profileId)
    {
        $profile = DirectoryProfile::find($profileId);
        if ($profile) {
            $profile->is_public = !$profile->is_public;
            $profile->save();
        }
    }

    public function toggleVerification($profileId)
    {
        $profile = DirectoryProfile::find($profileId);
        if ($profile) {
            $profile->is_verified = !$profile->is_verified;
            $profile->save();
        }
    }

    public function confirmDeletion($profileId)
    {
        $this->confirmingDeletion = true;
        $this->selectedProfileId = $profileId;
    }

    public function deleteProfile()
    {
        $profile = DirectoryProfile::find($this->selectedProfileId);
        if ($profile) {
            // We only delete the profile data, not the user
            $profile->delete();
        }
        $this->confirmingDeletion = false;
        $this->selectedProfileId = null;
    }

    public function getGlobalStatsProperty()
    {
        $totalViews = DirectoryAnalytic::where('event_type', 'profile_view')->count();
        $totalContacts = DirectoryAnalytic::where('event_type', 'whatsapp_click')->count();
        $totalProfiles = DirectoryProfile::count();
        
        $conversionRate = $totalViews > 0 ? round(($totalContacts / $totalViews) * 100, 1) : 0;

        // Top 3 lawyers with most views in the last 30 days
        $topProfiles = DirectoryProfile::with('user')
            ->select('directory_profiles.*')
            ->join('directory_analytics', 'directory_profiles.id', '=', 'directory_analytics.directory_profile_id')
            ->where('directory_analytics.event_type', 'profile_view')
            ->where('directory_analytics.event_date', '>=', now()->subDays(30))
            ->groupBy('directory_profiles.id')
            ->orderByRaw('COUNT(directory_analytics.id) DESC')
            ->limit(3)
            ->get();

        return [
            'total_views' => $totalViews,
            'total_contacts' => $totalContacts,
            'total_profiles' => $totalProfiles,
            'conversion_rate' => $conversionRate,
            'top_profiles' => $topProfiles
        ];
    }

    public function render()
    {
        $profiles = DirectoryProfile::with('user')
            ->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orWhere('headline', 'like', '%' . $this->search . '%')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin.directory.index', [
            'profiles' => $profiles,
            'stats'    => $this->globalStats
        ])->layout('layouts.app');
    }
}
