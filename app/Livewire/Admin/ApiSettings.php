<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ApiSettings extends Component
{
    public $tokenName = '';
    public $newToken = null;
    public $tokens = [];

    public function mount()
    {
        $this->loadTokens();
    }

    public function loadTokens()
    {
        $this->tokens = Auth::user()->tokens()->orderBy('created_at', 'desc')->get();
    }

    public function generateToken()
    {
        $this->validate([
            'tokenName' => 'required|string|min:3|max:50',
        ]);

        $token = Auth::user()->createToken($this->tokenName);
        $this->newToken = $token->plainTextToken;
        
        $this->tokenName = '';
        $this->loadTokens();
        $this->dispatch('notify', 'Token generado exitosamente.');
    }

    public function revokeToken($tokenId)
    {
        Auth::user()->tokens()->where('id', $tokenId)->delete();
        $this->loadTokens();
        $this->dispatch('notify', 'Token revocado correctamente.');
    }

    public function clearNewToken()
    {
        $this->newToken = null;
    }

    public function render()
    {
        return view('livewire.admin.api-settings')->layout('layouts.app');
    }
}
