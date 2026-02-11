<?php

namespace App\Livewire\Library;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class TemplateLibrary extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $selectedCategory = 'Todos';
    public $selectedTemplate = null;
    public $showPreview = false;
    public $showUploadModal = false;

    // Upload Fields
    public $newTemplateName;
    public $newTemplateDescription;
    public $newTemplateCategory;
    public $newTemplateMateria;
    public $newTemplateFile;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => 'Todos'],
    ];

    public function openUploadModal()
    {
        $this->reset(['newTemplateName', 'newTemplateDescription', 'newTemplateCategory', 'newTemplateMateria', 'newTemplateFile']);
        $this->showUploadModal = true;
    }

    public function saveTemplate()
    {
        $this->validate([
            'newTemplateName' => 'required|min:3',
            'newTemplateCategory' => 'required',
            'newTemplateMateria' => 'required',
            'newTemplateFile' => 'required|max:10240', // 10MB max
        ]);

        $extension = $this->newTemplateFile->getClientOriginalExtension();
        $path = $this->newTemplateFile->store('templates', 'public');
        $fullPath = storage_path('app/public/' . $path);
        
        $extractedText = '';

        try {
            if ($extension === 'pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($fullPath);
                $extractedText = $pdf->getText();
            } elseif (in_array($extension, ['docx', 'doc'])) {
                $extractedText = $this->readDocx($fullPath);
            } elseif ($extension === 'txt') {
                $extractedText = file_get_contents($fullPath);
            }
        } catch (\Exception $e) {
            \Log::error("Template extraction failed: " . $e->getMessage());
        }

        // Simple placeholder detection
        preg_match_all('/\[(.*?)\]/', $extractedText, $matches);
        $placeholders = array_unique($matches[0] ?? []);

        $template = \App\Models\LegalTemplate::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $this->newTemplateName,
            'description' => $this->newTemplateDescription,
            'category' => $this->newTemplateCategory,
            'materia' => $this->newTemplateMateria,
            'file_path' => $path,
            'extension' => $extension,
            'is_global' => false,
            'extracted_text' => $extractedText,
            'placeholders' => $placeholders,
        ]);

        $this->showUploadModal = false;
        $this->dispatch('notify', 'Formato subido y procesado exitosamente.');
        $this->resetPage();
    }

    private function readDocx($filePath) {
        $content = '';
        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            if (($index = $zip->locateName('word/document.xml')) !== false) {
                $data = $zip->getFromIndex($index);
                $content = strip_tags($data, '<w:p><w:t>');
                $content = preg_replace('/<w:p.*?>/', "\n", $content);
                $content = strip_tags($content);
            }
            $zip->close();
        }
        return $content;
    }

    public function personalizeTemplate($id)
    {
        $this->dispatch('notify', [
            'message' => 'El asistente de IA está preparando el editor para este formato...',
            'type' => 'info'
        ]);
        // Future: Redirect to an editor or open a placeholder filling modal
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function selectCategory($category)
    {
        $this->selectedCategory = $category;
        $this->resetPage();
    }

    public function selectTemplate($id)
    {
        $this->selectedTemplate = \App\Models\LegalTemplate::find($id);
        $this->showPreview = true;
    }

    public function closePreview()
    {
        $this->showPreview = false;
        $this->selectedTemplate = null;
    }

    public function render()
    {
        $query = \App\Models\LegalTemplate::forTenant(auth()->user()->tenant_id);

        if ($this->selectedCategory !== 'Todos') {
            $query->where('category', $this->selectedCategory);
        }

        if (!empty($this->search)) {
            // Hibrid Search: FullText + Like fallback
            $searchTerms = collect(explode(' ', $this->search))
                ->filter()
                ->map(fn($term) => "{$term}*")
                ->implode(' ');

            $searchTermRaw = $this->search;
            $query->where(function($q) use ($searchTerms, $searchTermRaw) {
                $q->whereRaw("MATCH(name, description, extracted_text) AGAINST(? IN BOOLEAN MODE)", [$searchTerms])
                  ->orWhere('name', 'like', "%{$searchTermRaw}%");
            })->orderByRaw("MATCH(name, description, extracted_text) AGAINST(? IN BOOLEAN MODE) DESC", [$searchTerms]);
        } else {
            $query->latest();
        }

        $categories = \App\Models\LegalTemplate::forTenant(auth()->user()->tenant_id)
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('livewire.library.template-library', [
            'templates' => $query->paginate(12),
            'categories' => $categories,
            'selectedTemplate' => $this->selectedTemplate
        ])->layout('layouts.app');
    }
}
