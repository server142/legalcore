<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Actuacion;
use Illuminate\Support\Facades\Log;

class PushNotifications extends Component
{
    public $urgentCount = 0;
    public $lastChecked;

    public function mount()
    {
        $this->lastChecked = now();
        $this->checkDeadlines();
    }

    public function checkDeadlines()
    {
        if (!auth()->check()) return;

        $user = auth()->user();
        
        // Find deadlines occurring in the next 24 hours that are pending
        $query = Actuacion::where('es_plazo', true)
            ->where('estado', 'pendiente')
            ->where('fecha_vencimiento', '>=', now())
            ->where('fecha_vencimiento', '<=', now()->addHours(24));

        // Security: Filter by user access
        if ($user->hasRole('abogado') && !$user->can('view all terminos')) {
            $query->whereHas('expediente', function($q) use ($user) {
                $q->where('abogado_responsable_id', $user->id)
                  ->orWhereHas('assignedUsers', function($q2) use ($user) {
                      $q2->where('users.id', $user->id);
                  });
            });
        }

        $deadlines = $query->get();
        $this->urgentCount = $deadlines->count();

        if ($this->urgentCount > 0) {
            foreach ($deadlines as $deadline) {
                // We could use a session or cache to avoid repeated alerts in the same session
                $cacheKey = 'deadine_alert_' . $user->id . '_' . $deadline->id;
                if (!cache()->has($cacheKey)) {
                    $this->dispatch('urgent-deadline-alert', [
                        'title' => '¡Vencimiento Inminente!',
                        'message' => "Término: {$deadline->titulo} vence en {$deadline->fecha_vencimiento->diffForHumans()}",
                        'url' => route('expedientes.show', $deadline->expediente_id)
                    ]);
                    cache()->put($cacheKey, true, now()->addHours(12));
                }
            }
        }
    }

    public function render()
    {
        return <<<'HTML'
            <div wire:poll.300s="checkDeadlines">
                {{-- Invisible logic component --}}
            </div>
        HTML;
    }
}
