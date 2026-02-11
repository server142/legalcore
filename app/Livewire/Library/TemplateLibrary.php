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
        
        // Correct path for extraction
        $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
        
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
            $extractedText = "Error al extraer contenido: " . $e->getMessage();
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
        try {
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
        } catch (\Exception $e) {
            \Log::error("Docx parsing failed: " . $e->getMessage());
        }
        return $content;
    }

    public $showPersonalizeModal = false;
    public $formPlaceholders = [];

    public function personalizeTemplate($id)
    {
        // 1. First, clear any previous preview state to avoid conflicts
        $this->showPreview = false;
        
        $this->selectedTemplate = \App\Models\LegalTemplate::find($id);
        
        if (!$this->selectedTemplate) return;

        // 2. Clear and initialize placeholders
        $this->formPlaceholders = [];
        if (!empty($this->selectedTemplate->placeholders)) {
            // Ensure we handle both JSON strings and arrays
            $placeholders = is_string($this->selectedTemplate->placeholders) 
                ? json_decode($this->selectedTemplate->placeholders, true) 
                : $this->selectedTemplate->placeholders;

            foreach ($placeholders as $ph) {
                // Remove brackets for the form key
                $cleanKey = str_replace(['[', ']'], '', $ph);
                $this->formPlaceholders[$ph] = ''; 
            }
        }

        // 3. Open the modal explicitly
        $this->showPersonalizeModal = true;
    }

    public function deleteTemplate($id)
    {
        $template = \App\Models\LegalTemplate::where('tenant_id', auth()->user()->tenant_id)
            ->where('id', $id)
            ->first();

        if ($template) {
            // Delete file from storage
            \Illuminate\Support\Facades\Storage::disk('public')->delete($template->file_path);
            $template->delete();
            $this->showPreview = false;
            $this->dispatch('notify', 'Documento eliminado permanentemente.');
        } else {
            $this->dispatch('notify', [
                'message' => 'No tienes permisos para eliminar este documento.',
                'type' => 'error'
            ]);
        }
    }

    public function closePreview()
    {
        $this->showPreview = false;
        $this->showPersonalizeModal = false; // Ensure both are closed
        $this->selectedTemplate = null;
    }

    public function generateDocument()
    {
        $this->validate([
            'formPlaceholders.*' => 'required'
        ]);

        if (!$this->selectedTemplate) return;

        $extension = strtolower($this->selectedTemplate->extension);
        $originalPath = \Illuminate\Support\Facades\Storage::disk('public')->path($this->selectedTemplate->file_path);
        
        // Final filename
        $cleanName = str_replace(' ', '_', $this->selectedTemplate->name);
        $newFileName = 'Personalizado_' . $cleanName . '.' . $extension;

        if ($extension === 'docx') {
            // Real DOCX Replacement Logic
            $tempFile = storage_path('app/public/temp_' . time() . '.docx');
            copy($originalPath, $tempFile);

            $zip = new \ZipArchive();
            if ($zip->open($tempFile) === true) {
                // Read the main document content
                if (($index = $zip->locateName('word/document.xml')) !== false) {
                    $xmlContent = $zip->getFromIndex($index);
                    
                    foreach ($this->formPlaceholders as $placeholder => $value) {
                        // We use a regex to handle cases where Word splits tags like [ <tag> VARIABLE </tag> ]
                        // This is a common issue with Word XML.
                        // We'll try a simple replace first, then a regex-based one if needed.
                        $xmlContent = str_replace($placeholder, htmlspecialchars($value), $xmlContent);
                    }
                    
                    $zip->addFromString('word/document.xml', $xmlContent);
                }
                $zip->close();
                
                $this->showPersonalizeModal = false;
                $this->dispatch('notify', '¡Documento generado con éxito!');
                
                return response()->download($tempFile, $newFileName)->deleteFileAfterSend(true);
            }
        }

        // Fallback for PDF or others (just download original but with nice name)
        $this->showPersonalizeModal = false;
        $this->dispatch('notify', 'Descarga iniciada (Formato original).');
        
        return response()->download($originalPath, $newFileName);
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
