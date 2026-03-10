<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DirectoryProfile;
use Livewire\Attributes\Layout;

class PublicDirectoryProfile extends Component
{
    public DirectoryProfile $profile;

    public function mount(DirectoryProfile $profile)
    {
        // Solo mostrar perfiles públicos (o al dueño si está logueado, pero es público por ruta)
        if (!$profile->is_public && auth()->id() !== $profile->user_id) {
            abort(404);
        }

        $this->profile = $profile->load('user', 'user.tenant');

        // Registrar visita (no contar al propio dueño)
        if (auth()->id() !== $profile->user_id) {
            $profile->trackEvent('profile_view');
        }
    }

    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.public-directory-profile');
    }
}
