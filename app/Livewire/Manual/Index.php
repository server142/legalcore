<?php

namespace App\Livewire\Manual;

use Livewire\Component;
use App\Models\ManualPage;

class Index extends Component
{
    public function render()
    {
        $pages = ManualPage::orderBy('order')->get();

        return view('livewire.manual.index', [
            'pages' => $pages
        ]);
    }
}
