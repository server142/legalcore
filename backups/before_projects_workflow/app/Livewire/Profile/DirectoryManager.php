<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\DirectoryProfile;

class DirectoryManager extends Component
{
    use WithFileUploads;

    public $photo; // Uploaded file (TemporaryUploadedFile)
    public $headline;
    public $bio;
    public $specialties = [];
    public $newSpecialty = '';
    public $city;
    public $state;
    public $professional_license;
    public $whatsapp;
    public $website;
    public $linkedin;
    public $is_public = false;
    
    // UI State
    public $showSavedMessage = false;

    protected function rules()
    {
        return [
            'headline'             => 'nullable|string|max:100',
            'bio'                  => 'nullable|string|max:1000',
            'specialties'          => 'array|max:10',
            'city'                 => 'nullable|string|max:50',
            'state'                => 'nullable|string|max:50',
            'professional_license' => 'nullable|string|max:20',
            'whatsapp'             => 'nullable|string|max:20',
            'website'              => 'nullable|url|max:255',
            'linkedin'             => 'nullable|url|max:255',
            'is_public'            => 'boolean',
            'photo'                => 'nullable|image|max:4096',
        ];
    }

    public function mount()
    {
        $user    = Auth::user();
        $profile = DirectoryProfile::firstOrCreate(['user_id' => $user->id]);

        $this->headline             = $profile->headline;
        $this->bio                  = $profile->bio;
        $this->specialties          = $profile->specialties ?? [];
        $this->city                 = $profile->city;
        $this->state                = $profile->state;
        $this->professional_license = $profile->professional_license;
        $this->whatsapp             = $profile->whatsapp;
        $this->website              = $profile->website;
        $this->linkedin             = $profile->linkedin;
        $this->is_public            = $profile->is_public;
    }

    public function addSpecialty()
    {
        if (trim($this->newSpecialty) === '') return;

        if (!in_array($this->newSpecialty, $this->specialties) && count($this->specialties) < 10) {
            $this->specialties[] = $this->newSpecialty;
        }

        $this->newSpecialty = '';
    }

    public function removeSpecialty($index)
    {
        unset($this->specialties[$index]);
        $this->specialties = array_values($this->specialties);
    }

    /**
     * Guarda SOLO la foto del directorio de forma independiente.
     * Se llama automáticamente desde JS cuando el usuario confirma el recorte.
     */
    public function savePhoto()
    {
        $this->validateOnly('photo');

        if (!$this->photo) {
            return;
        }

        $disk    = isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : 'public';
        $profile = Auth::user()->directoryProfile;

        // Eliminar foto anterior
        if ($profile->profile_photo_path) {
            Storage::disk($disk)->delete($profile->profile_photo_path);
        }

        // Guardar nueva foto
        $path = $this->photo->store('directory-photos', $disk);
        $profile->update(['profile_photo_path' => $path]);

        $this->photo = null; // Limpiar temporal

        $this->dispatch('notify', message: '✓ Foto de perfil actualizada correctamente.');
    }

    public function deleteProfilePhoto()
    {
        $disk    = isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : 'public';
        $profile = Auth::user()->directoryProfile;

        if ($profile->profile_photo_path) {
            Storage::disk($disk)->delete($profile->profile_photo_path);
            $profile->update(['profile_photo_path' => null]);
        }

        $this->dispatch('notify', message: 'Foto de perfil eliminada.');
    }

    /**
     * Guarda los campos de texto del perfil.
     */
    public function save()
    {
        $this->validate(collect($this->rules())->except('photo')->toArray());

        $profile = Auth::user()->directoryProfile;

        $profile->headline             = $this->headline;
        $profile->bio                  = $this->bio;
        $profile->specialties          = $this->specialties;
        $profile->city                 = $this->city;
        $profile->state                = $this->state;
        $profile->professional_license = $this->professional_license;
        $profile->whatsapp             = $this->whatsapp;
        $profile->website              = $this->website;
        $profile->linkedin             = $this->linkedin;
        $profile->is_public            = $this->is_public;

        $profile->save();

        $this->showSavedMessage = true;
        $this->dispatch('notify', message: '✓ Perfil guardado correctamente.');
    }

    public function render()
    {
        return view('livewire.profile.directory-manager', [
            'user' => Auth::user()->load('directoryProfile'),
        ]);
    }
}

