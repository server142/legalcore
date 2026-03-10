<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;

use App\Models\Expediente;
use App\Models\Cliente as ClienteModel;

class Cliente extends Component
{
    public $expedientes;

    public function mount()
    {
        // Find the client record associated with this user (assuming email match or similar logic)
        $clienteRecord = ClienteModel::where('email', auth()->user()->email)->first();

        if ($clienteRecord) {
            $this->expedientes = Expediente::where('cliente_id', $clienteRecord->id)
                ->with('actuaciones')
                ->latest()
                ->get();
        } else {
            $this->expedientes = collect();
        }
    }

    public function render()
    {
        return view('livewire.dashboards.cliente');
    }
}
