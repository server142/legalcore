<?php

namespace App\Livewire\Admin\Announcements;

use Livewire\Component;
use App\Models\Announcement;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    
    // Modal Fields
    public $announcementId;
    public $title;
    public $message;
    public $video_url;
    public $target_role = 'all'; // all, super_admin, tenant_admin, regular_user
    public $is_active = true;

    public $confirmingAnnouncementDeletion = false;
    public $confirmingAnnouncementManagement = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'message' => 'nullable|string',
        'video_url' => 'nullable|url',
        'target_role' => 'nullable|string|in:all,super_admin,tenant_admin,regular_user',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $announcements = Announcement::where('title', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.announcements.index', [
            'announcements' => $announcements
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->confirmingAnnouncementManagement = true;
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->announcementId = $id;
        $this->title = $announcement->title;
        $this->message = $announcement->message;
        $this->video_url = $announcement->video_url;
        $this->target_role = $announcement->target_role ?? 'all';
        $this->is_active = $announcement->is_active;

        $this->confirmingAnnouncementManagement = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'message' => $this->message,
            'video_url' => $this->video_url,
            'target_role' => $this->target_role === 'all' ? null : $this->target_role,
            'is_active' => $this->is_active,
        ];

        if ($this->announcementId) {
            $announcement = Announcement::find($this->announcementId);
            $announcement->update($data);
            session()->flash('message', 'Anuncio actualizado correctamente.');
        } else {
            Announcement::create($data);
            session()->flash('message', 'Anuncio creado y publicado (si estaba activo).');
        }

        $this->confirmingAnnouncementManagement = false;
        $this->resetInputFields();
    }

    public function delete($id)
    {
        $this->announcementId = $id;
        $this->confirmingAnnouncementDeletion = true;
    }

    public function confirmDelete()
    {
        Announcement::find($this->announcementId)->delete();
        $this->confirmingAnnouncementDeletion = false;
        $this->resetInputFields();
        session()->flash('message', 'Anuncio eliminado correctamente.');
    }

    public function toggleStatus($id)
    {
        $announcement = Announcement::find($id);
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();
    }

    private function resetInputFields()
    {
        $this->title = '';
        $this->message = '';
        $this->video_url = '';
        $this->target_role = 'all';
        $this->is_active = true;
        $this->announcementId = null;
    }
}
