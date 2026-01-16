<?php

namespace App\Livewire\Manual;

use Livewire\Component;
use App\Models\ManualPage;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class Manage extends Component
{
    use WithFileUploads;

    public $pages;
    public $showModal = false;
    public $editMode = false;
    public $pageId;
    
    public $title;
    public $content;
    public $order = 0;
    public $image;
    public $existingImage;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'order' => 'required|integer',
        'image' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }
        $this->loadPages();
    }

    public function loadPages()
    {
        $this->pages = ManualPage::orderBy('order')->get();
    }

    public function create()
    {
        $this->reset(['title', 'content', 'order', 'image', 'existingImage', 'pageId', 'editMode']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $page = ManualPage::findOrFail($id);
        $this->pageId = $page->id;
        $this->title = $page->title;
        $this->content = $page->content;
        $this->order = $page->order;
        $this->existingImage = $page->image_path;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'content' => $this->content,
            'order' => $this->order,
        ];

        if ($this->image) {
            $data['image_path'] = $this->image->store('manual', 'public');
        }

        if ($this->editMode) {
            ManualPage::find($this->pageId)->update($data);
        } else {
            ManualPage::create($data);
        }

        $this->showModal = false;
        $this->loadPages();
        $this->dispatch('notify', 'Página del manual guardada correctamente');
    }

    public function delete($id)
    {
        ManualPage::find($id)->delete();
        $this->loadPages();
    }

    public function render()
    {
        return view('livewire.manual.manage');
    }
}
