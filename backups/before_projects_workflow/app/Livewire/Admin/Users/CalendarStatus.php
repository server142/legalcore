<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CalendarStatus extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all'; // all, configured, missing

    public function render()
    {
        $query = User::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with('roles');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus === 'configured') {
            $query->whereNotNull('calendar_email');
        } elseif ($this->filterStatus === 'missing') {
            $query->whereNull('calendar_email');
        }

        $users = $query->latest()->paginate(20);

        $stats = [
            'total' => User::where('tenant_id', auth()->user()->tenant_id)->count(),
            'configured' => User::where('tenant_id', auth()->user()->tenant_id)->whereNotNull('calendar_email')->count(),
            'missing' => User::where('tenant_id', auth()->user()->tenant_id)->whereNull('calendar_email')->count(),
        ];

        return view('livewire.admin.users.calendar-status', [
            'users' => $users,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
