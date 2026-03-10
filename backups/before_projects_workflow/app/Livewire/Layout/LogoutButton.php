<?php

namespace App\Livewire\Layout;

use App\Livewire\Actions\Logout;
use Livewire\Component;

class LogoutButton extends Component
{
    public function logout(Logout $logout): void
    {
        $plan = auth()->user()->tenant->plan ?? '';
        $isDirOnly = str_contains($plan, 'directory');

        $logout();

        if ($isDirOnly) {
            $this->redirect('/directorio', navigate: true);
        } else {
            $this->redirect('/', navigate: true);
        }
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
