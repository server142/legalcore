<?php

namespace App\Livewire\Admin\Directory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DirectoryProfile;
use App\Models\User;

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
            'profiles' => $profiles
        ])->layout('layouts.app');
    }
}
