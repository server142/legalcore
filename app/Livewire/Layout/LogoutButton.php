<?php

namespace App\Livewire\Layout;

use App\Livewire\Actions\Logout;
use Livewire\Component;

class LogoutButton extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return <<<'HTML'
            <x-dropdown-link wire:click="logout" class="cursor-pointer">
                {{ __('Cerrar Sesión') }}
            </x-dropdown-link>
        HTML;
    }
}
