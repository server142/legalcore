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
            $this->redirect(route('directory.public'), navigate: true);
        } else {
            // For Premium, try to redirect back to the marketing landing unless they want to stay on the directory
            $this->redirect(route('welcome'), navigate: true);
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
