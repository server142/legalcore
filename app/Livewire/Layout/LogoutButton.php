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
            <button wire:click="logout" class="w-full text-start">
                <x-dropdown-link>
                    {{ __('Cerrar Sesión') }}
                </x-dropdown-link>
            </button>
        HTML;
    }
}
