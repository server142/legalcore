<?php

namespace App\Livewire\Agenda;

use Livewire\Component;
use App\Models\Evento;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Index extends Component
{
    public $eventos;
    public $calendarEvents = [];

    public function mount()
    {
        if (!auth()->user()->can('view agenda')) {
            abort(403);
        }

        $query = Evento::with('user');

        // Si es abogado y no tiene permiso de ver todo, filtrar por sus eventos o sus expedientes
        if (auth()->user()->hasRole('abogado') && !auth()->user()->can('view all expedientes')) {
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhereHas('expediente', function($qe) {
                      $qe->where('abogado_responsable_id', auth()->id())
                         ->orWhereHas('assignedUsers', function($qu) {
                             $qu->where('users.id', auth()->id());
                         });
                  })
                  ->orWhereHas('invitedUsers', function($qi) {
                      $qi->where('users.id', auth()->id());
                  });
            });
        }

        // Próximos eventos para el sidebar
        $this->eventos = (clone $query)->where('start_time', '>=', now()->startOfDay())
            ->orderBy('start_time')
            ->take(10)
            ->get();

        // Formato para FullCalendar
        $allEvents = $query->get();
        $isAdmin = auth()->user()->hasRole(['admin', 'super_admin']);

        foreach ($allEvents as $evento) {
            $color = '#3b82f6'; // Default blue
            if ($evento->tipo == 'audiencia') $color = '#ef4444'; // Red
            if ($evento->tipo == 'termino') $color = '#f97316'; // Orange

            $title = $evento->titulo;

            // Icono según si es de expediente o personal
            $icon = $evento->expediente_id ? "📂 " : "👤 ";
            $title = $icon . $title;

            if ($isAdmin && $evento->user_id !== auth()->id()) {
                $title = "[" . $evento->user->name . "] " . $title;
                $color = $this->adjustColor($color, $evento->user_id);
            }

            if ($evento->google_event_id) {
                $title .= ' ☁️';
            }

            $this->calendarEvents[] = [
                'id' => $evento->id,
                'title' => $title,
                'start' => $evento->start_time->toIso8601String(),
                'end' => $evento->end_time ? $evento->end_time->toIso8601String() : null,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'expediente_id' => $evento->expediente_id,
                    'tipo' => $evento->tipo,
                    'user_name' => $evento->user->name,
                    'google_event_id' => $evento->google_event_id,
                    'description' => $evento->descripcion
                ]
            ];
        }
    }

    public function applySuggestedSlot()
    {
        if ($this->suggested_start) {
            $this->start = $this->suggested_start;
        }
        if ($this->suggested_end) {
            $this->end = $this->suggested_end;
        }

        $this->suggested_start = null;
        $this->suggested_end = null;
    }

    private function hasAgendaConflict(Carbon $start, Carbon $end, int $userId, ?int $ignoreEventId): bool
    {
        $query = Evento::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('user_id', $userId)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start);

        if ($ignoreEventId) {
            $query->where('id', '!=', $ignoreEventId);
        }

        return $query->exists();
    }

    private function suggestNextAvailableSlot(Carbon $requestedStart, int $durationMinutes, int $userId, ?int $ignoreEventId): ?array
    {
        $tenant = auth()->user()->tenant;
        $settings = $tenant?->settings ?? [];

        $workStart = $settings['asesorias_working_hours_start'] ?? '09:00';
        $workEnd = $settings['asesorias_working_hours_end'] ?? '18:00';
        $businessDays = $settings['asesorias_business_days'] ?? ['mon', 'tue', 'wed', 'thu', 'fri'];
        $slotMinutes = (int) ($settings['asesorias_slot_minutes'] ?? 15);

        $candidate = (clone $requestedStart);
        $candidate->second(0);

        if ($slotMinutes > 0) {
            $minute = (int) $candidate->format('i');
            $rounded = (int) (ceil($minute / $slotMinutes) * $slotMinutes);
            if ($rounded >= 60) {
                $candidate->addHour()->minute(0);
            } else {
                $candidate->minute($rounded);
            }
        }

        for ($i = 0; $i < 2000; $i++) {
            $dayKey = strtolower($candidate->format('D'));
            $dayKey = substr($dayKey, 0, 3);

            if (!in_array($dayKey, $businessDays, true)) {
                $candidate->addDay()->setTimeFromTimeString($workStart);
                continue;
            }

            $startLimit = (clone $candidate)->setTimeFromTimeString($workStart);
            $endLimit = (clone $candidate)->setTimeFromTimeString($workEnd);

            if ($candidate->lt($startLimit)) {
                $candidate = $startLimit;
            }

            $candidateEnd = (clone $candidate)->addMinutes($durationMinutes);
            if ($candidateEnd->gt($endLimit)) {
                $candidate->addDay()->setTimeFromTimeString($workStart);
                continue;
            }

            if (!$this->hasAgendaConflict($candidate, $candidateEnd, $userId, $ignoreEventId)) {
                return ['start' => $candidate, 'end' => $candidateEnd];
            }

            $candidate->addMinutes(max(5, $slotMinutes));
        }

        return null;
    }

    private function adjustColor($hex, $userId)
    {
        // Simple logic to vary color based on user ID for admins
        // We'll use a set of predefined colors or just shift the hue/brightness
        $colors = [
            '#3b82f6', '#ef4444', '#f97316', '#10b981', '#8b5cf6', 
            '#ec4899', '#06b6d4', '#f59e0b', '#6366f1', '#14b8a6'
        ];
        
        return $colors[$userId % count($colors)];
    }

    public $showModal = false;
    public $editMode = false;
    public $eventId;
    public $title;
    public $description;
    public $start;
    public $end;
    public $type = 'audiencia';
    public $expediente_id;
    public $selectedUsers = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start' => 'required|date',
        'end' => 'nullable|date|after:start',
        'type' => 'required|in:audiencia,termino,cita',
        'expediente_id' => 'nullable|exists:expedientes,id',
        'selectedUsers' => 'nullable|array',
        'selectedUsers.*' => 'exists:users,id',
    ];

    public $suggested_start;
    public $suggested_end;

    public function create()
    {
        $this->reset(['title', 'description', 'start', 'end', 'type', 'expediente_id', 'eventId', 'editMode', 'selectedUsers', 'suggested_start', 'suggested_end']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->editMode = true;
        $this->eventId = $id;
        $this->suggested_start = null;
        $this->suggested_end = null;
        $evento = Evento::with('invitedUsers')->findOrFail($id);
        
        $this->title = $evento->titulo;
        $this->description = $evento->descripcion;
        $this->start = $evento->start_time->format('Y-m-d\TH:i');
        $this->end = $evento->end_time ? $evento->end_time->format('Y-m-d\TH:i') : null;
        $this->type = $evento->tipo;
        $this->expediente_id = $evento->expediente_id;
        $this->selectedUsers = $evento->invitedUsers->pluck('id')->toArray();
        
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        $tenant = auth()->user()->tenant;
        $settings = $tenant?->settings ?? [];
        $enforceAvailability = (bool) ($settings['agenda_enforce_availability'] ?? false);

        $start = Carbon::parse($this->start);
        $end = $this->end ? Carbon::parse($this->end) : null;
        if (!$end) {
            $end = (clone $start)->addHour();
        }

        if ($enforceAvailability) {
            if ($this->hasAgendaConflict($start, $end, auth()->id(), null)) {
                $suggestion = $this->suggestNextAvailableSlot($start, $end->diffInMinutes($start), auth()->id(), null);
                if ($suggestion) {
                    $this->suggested_start = $suggestion['start']->format('Y-m-d\TH:i');
                    $this->suggested_end = $suggestion['end']->format('Y-m-d\TH:i');
                    $this->addError('start', 'Tienes un evento traslapado en ese horario. Te proponemos el siguiente horario disponible.');
                } else {
                    $this->addError('start', 'Tienes un evento traslapado en ese horario.');
                }
                return;
            }
        }

        $evento = Evento::create([
            'tenant_id' => auth()->user()->tenant_id,
            'titulo' => $this->title,
            'descripcion' => $this->description,
            'start_time' => $start,
            'end_time' => $end,
            'tipo' => $this->type,
            'expediente_id' => $this->expediente_id ?: null,
            'user_id' => auth()->id(),
        ]);

        if (!empty($this->selectedUsers)) {
            Log::info('Agenda Store: Syncing users', ['count' => count($this->selectedUsers), 'users' => $this->selectedUsers]);
            $evento->invitedUsers()->sync($this->selectedUsers);
            $evento->touch(); // Trigger updated observer to sync attendees to Google
        } else {
            Log::info('Agenda Store: No users selected for sync');
        }

        $this->showModal = false;
        $this->dispatch('notify', 'Evento creado exitosamente');
        $this->redirect(route('agenda.index'), navigate: true);
    }

    public function update()
    {
        $this->validate();

        $tenant = auth()->user()->tenant;
        $settings = $tenant?->settings ?? [];
        $enforceAvailability = (bool) ($settings['agenda_enforce_availability'] ?? false);

        $start = Carbon::parse($this->start);
        $end = $this->end ? Carbon::parse($this->end) : null;
        if (!$end) {
            $end = (clone $start)->addHour();
        }

        if ($enforceAvailability) {
            if ($this->hasAgendaConflict($start, $end, auth()->id(), (int) $this->eventId)) {
                $suggestion = $this->suggestNextAvailableSlot($start, $end->diffInMinutes($start), auth()->id(), (int) $this->eventId);
                if ($suggestion) {
                    $this->suggested_start = $suggestion['start']->format('Y-m-d\TH:i');
                    $this->suggested_end = $suggestion['end']->format('Y-m-d\TH:i');
                    $this->addError('start', 'Tienes un evento traslapado en ese horario. Te proponemos el siguiente horario disponible.');
                } else {
                    $this->addError('start', 'Tienes un evento traslapado en ese horario.');
                }
                return;
            }
        }

        $evento = Evento::findOrFail($this->eventId);
        $evento->update([
            'titulo' => $this->title,
            'descripcion' => $this->description,
            'start_time' => $start,
            'end_time' => $end,
            'tipo' => $this->type,
            'expediente_id' => $this->expediente_id ?: null,
        ]);

        Log::info('Agenda Update: Syncing users', ['count' => count($this->selectedUsers), 'users' => $this->selectedUsers]);
        $evento->invitedUsers()->sync($this->selectedUsers);
        $evento->touch();

        $this->showModal = false;
        $this->dispatch('notify', 'Evento actualizado exitosamente');
        $this->redirect(route('agenda.index'), navigate: true);
    }

    public $confirmingDeletion = false;
    public $itemToDeleteId;

    public function confirmDelete($id)
    {
        $this->itemToDeleteId = $id;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        $evento = Evento::findOrFail($this->itemToDeleteId);
        $evento->invitedUsers()->detach();
        $evento->delete();
        $this->confirmingDeletion = false;
        $this->dispatch('notify', 'Evento eliminado exitosamente');
        $this->redirect(route('agenda.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.agenda.index', [
            'expedientes' => \App\Models\Expediente::where('tenant_id', auth()->user()->tenant_id)->get(),
            'abogados' => \App\Models\User::where('tenant_id', auth()->user()->tenant_id)
                ->where('id', '!=', auth()->id())
                ->get()
        ]);
    }
}
