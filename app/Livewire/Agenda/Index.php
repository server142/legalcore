<?php

namespace App\Livewire\Agenda;

use Livewire\Component;
use App\Models\Evento;

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
                    'user_name' => $evento->user->name
                ]
            ];
        }
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

    public function create()
    {
        $this->reset(['title', 'description', 'start', 'end', 'type', 'expediente_id', 'eventId', 'editMode', 'selectedUsers']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->editMode = true;
        $this->eventId = $id;
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

        $evento = Evento::create([
            'tenant_id' => auth()->user()->tenant_id,
            'titulo' => $this->title,
            'descripcion' => $this->description,
            'start_time' => $this->start,
            'end_time' => $this->end,
            'tipo' => $this->type,
            'expediente_id' => $this->expediente_id ?: null,
            'user_id' => auth()->id(),
        ]);

        if (!empty($this->selectedUsers)) {
            $evento->invitedUsers()->sync($this->selectedUsers);
        }

        $this->showModal = false;
        $this->dispatch('notify', 'Evento creado exitosamente');
        $this->redirect(route('agenda.index'), navigate: true);
    }

    public function update()
    {
        $this->validate();

        $evento = Evento::findOrFail($this->eventId);
        $evento->update([
            'titulo' => $this->title,
            'descripcion' => $this->description,
            'start_time' => $this->start,
            'end_time' => $this->end,
            'tipo' => $this->type,
            'expediente_id' => $this->expediente_id ?: null,
        ]);

        $evento->invitedUsers()->sync($this->selectedUsers);

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
