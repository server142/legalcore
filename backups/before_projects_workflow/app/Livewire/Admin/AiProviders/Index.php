<?php

namespace App\Livewire\Admin\AiProviders;

use Livewire\Component;
use App\Models\AiProvider;
use Illuminate\Support\Str;

class Index extends Component
{
    public $providers = [];
    public $showModal = false;
    public $editingId = null;
    
    // Form fields
    public $name = '';
    public $slug = '';
    public $api_key = '';
    public $default_model = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:100',
        'slug' => 'required|string|max:50|unique:ai_providers,slug',
        'api_key' => 'required|string',
        'default_model' => 'required|string|max:100',
        'is_active' => 'boolean',
    ];

    public function mount()
    {
        $this->loadProviders();
    }

    public function loadProviders()
    {
        $this->providers = AiProvider::orderBy('sort_order')->orderBy('name')->get();
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $provider = AiProvider::findOrFail($id);
        
        $this->editingId = $provider->id;
        $this->name = $provider->name;
        $this->slug = $provider->slug;
        $this->api_key = $provider->api_key; // Will be decrypted automatically
        $this->default_model = $provider->default_model;
        $this->is_active = $provider->is_active;
        
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->editingId) {
            $this->rules['slug'] = 'required|string|max:50|unique:ai_providers,slug,' . $this->editingId;
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'api_key' => $this->api_key,
            'default_model' => $this->default_model,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            $provider = AiProvider::findOrFail($this->editingId);
            $provider->update($data);
            $message = 'Proveedor actualizado exitosamente.';
        } else {
            AiProvider::create($data);
            $message = 'Proveedor agregado exitosamente.';
        }

        $this->showModal = false;
        $this->resetForm();
        $this->loadProviders();
        $this->dispatch('notify', $message);
    }

    public function delete($id)
    {
        $provider = AiProvider::findOrFail($id);
        
        // Check if it's the active provider
        $activeProvider = AiProvider::getActive();
        if ($activeProvider && $activeProvider->id === $provider->id) {
            $this->dispatch('notify', 'No puedes eliminar el proveedor activo. Cambia a otro primero.');
            return;
        }

        $provider->delete();
        $this->loadProviders();
        $this->dispatch('notify', 'Proveedor eliminado.');
    }

    public function setActive($id)
    {
        $provider = AiProvider::findOrFail($id);
        $provider->setAsActive();
        
        $this->loadProviders();
        $this->dispatch('notify', "Proveedor '{$provider->name}' activado correctamente.");
    }

    public function testConnection($id)
    {
        $provider = AiProvider::findOrFail($id);
        
        // Temporarily set this provider as active for testing
        $originalActive = AiProvider::getActive();
        $provider->setAsActive();
        
        try {
            $aiService = app(\App\Services\AIService::class);
            $result = $aiService->ask([
                ['role' => 'user', 'content' => 'Responde solo con: OK']
            ], 0.1, 10);
            
            // Restore original active provider
            if ($originalActive) {
                $originalActive->setAsActive();
            }
            
            if (isset($result['success']) && $result['success']) {
                $this->dispatch('notify', "✅ Conexión exitosa con {$provider->name}");
            } else {
                $error = $result['error'] ?? 'Error desconocido';
                $this->dispatch('notify', "❌ Error: {$error}");
            }
        } catch (\Exception $e) {
            // Restore original active provider
            if ($originalActive) {
                $originalActive->setAsActive();
            }
            
            $this->dispatch('notify', "❌ Error de conexión: " . $e->getMessage());
        }
    }

    public function updatedName($value)
    {
        if (empty($this->editingId)) {
            $this->slug = Str::slug($value);
        }
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->slug = '';
        $this->api_key = '';
        $this->default_model = '';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.ai-providers.index')->layout('layouts.app');
    }
}
