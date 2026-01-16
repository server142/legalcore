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

        $query = Evento::query();

        // Si es abogado y no tiene permiso de ver todo, filtrar por sus eventos o sus expedientes
        if (auth()->user()->hasRole('abogado') && !auth()->user()->can('view all expedientes')) {
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhereHas('expediente', function($qe) {
                      $qe->where('abogado_responsable_id', auth()->id());
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
        foreach ($allEvents as $evento) {
            $color = '#3b82f6'; // Default blue
            if ($evento->tipo == 'audiencia') $color = '#ef4444'; // Red
            if ($evento->tipo == 'termino') $color = '#f97316'; // Orange

            $this->calendarEvents[] = [
                'id' => $evento->id,
                'title' => $evento->titulo,
                'start' => $evento->start_time->toIso8601String(),
                'end' => $evento->end_time ? $evento->end_time->toIso8601String() : null,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'expediente_id' => $evento->expediente_id,
                    'tipo' => $evento->tipo
                ]
            ];
        }
    }

    public $showModal = false;
    public $editMode = false;
    public $eventId;
    public $title;
    public $start;
    public $end;
    public $type = 'audiencia';
    public $expediente_id;

    protected $rules = [
        'title' => 'required|string|max:255',
        'start' => 'required|date',
        'end' => 'nullable|date|after:start',
        'type' => 'required|in:audiencia,termino,cita',
        'expediente_id' => 'nullable|exists:expedientes,id',
    ];

    public function create()
    {
        $this->reset(['title', 'start', 'end', 'type', 'expediente_id', 'eventId', 'editMode']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->editMode = true;
        $this->eventId = $id;
        $evento = Evento::findOrFail($id);
        
        $this->title = $evento->titulo;
        $this->start = $evento->start_time->format('Y-m-d\TH:i');
        $this->end = $evento->end_time ? $evento->end_time->format('Y-m-d\TH:i') : null;
        $this->type = $evento->tipo;
        $this->expediente_id = $evento->expediente_id;
        
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        Evento::create([
            'tenant_id' => auth()->user()->tenant_id,
            'titulo' => $this->title,
            'start_time' => $this->start,
            'end_time' => $this->end,
            'tipo' => $this->type,
            'expediente_id' => $this->expediente_id ?: null,
            'user_id' => auth()->id(),
        ]);

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
            'start_time' => $this->start,
            'end_time' => $this->end,
            'tipo' => $this->type,
            'expediente_id' => $this->expediente_id ?: null,
        ]);

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
        $evento->delete();
        $this->confirmingDeletion = false;
        $this->dispatch('notify', 'Evento eliminado exitosamente');
        $this->redirect(route('agenda.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.agenda.index', [
            'expedientes' => \App\Models\Expediente::where('tenant_id', auth()->user()->tenant_id)->get()
        ]);
    }
}
