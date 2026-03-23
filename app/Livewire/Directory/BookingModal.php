<?php

namespace App\Livewire\Directory;

use Livewire\Component;
use App\Models\DirectoryProfile;
use App\Models\Asesoria;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class BookingModal extends Component
{
    public $isOpen = false;
    public $profileId = null;
    public $lawyerName = '';
    
    // Form fields
    public $nombre = '';
    public $email = '';
    public $telefono = '';
    public $fecha = '';
    public $hora = '';
    public $asunto = '';
    public $tipo = 'videoconferencia'; // default
    
    public $success = false;

    // We make date selection default to tomorrow
    public function mount()
    {
        $this->fecha = Carbon::tomorrow()->format('Y-m-d');
        $this->hora = '10:00';
    }

    #[On('openBookingModal')]
    public function openModal($profileId)
    {
        $this->resetForm();
        
        $profile = DirectoryProfile::with('user.tenant')->find($profileId);
        
        if (!$profile || !$profile->user || !$profile->user->tenant) {
            return;
        }

        $this->profileId = $profileId;
        $this->lawyerName = $profile->user->name;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->success = false;
    }

    private function resetForm()
    {
        $this->reset(['nombre', 'email', 'telefono', 'asunto']);
        $this->fecha = Carbon::tomorrow()->format('Y-m-d');
        $this->hora = '10:00';
        $this->tipo = 'videoconferencia';
        $this->success = false;
        $this->resetValidation();
    }

    public function submit()
    {
        $this->validate([
            'nombre'     => 'required|string|max:100',
            'email'      => 'required|email|max:100',
            'telefono'   => 'required|string|max:20',
            'fecha'      => 'required|date|after_or_equal:today',
            'hora'       => 'required',
            'asunto'     => 'required|string|min:10|max:1000',
            'tipo'       => 'required|in:telefonica,videoconferencia,presencial',
        ]);

        $profile = DirectoryProfile::with('user.tenant')->find($this->profileId);
        
        if (!$profile) {
            $this->closeModal();
            return;
        }

        $fechaHora = Carbon::parse($this->fecha . ' ' . $this->hora);

        // Bypass typical multi-tenant scope manually by setting the specific tenant ID from the profile
        Asesoria::withoutGlobalScope('tenant')->create([
            'tenant_id'        => $profile->user->tenant_id,
            'abogado_id'       => $profile->user_id,
            'tipo'             => $this->tipo,
            'estado'           => 'agendada',
            'nombre_prospecto' => $this->nombre,
            'email'            => $this->email,
            'telefono'         => $this->telefono,
            'asunto'           => $this->asunto,
            'fecha_hora'       => $fechaHora,
            'duracion_minutos' => 60,
        ]);

        // Optional: Send notification to the lawyer here

        $this->success = true;
    }

    public function render()
    {
        return view('livewire.directory.booking-modal');
    }
}
