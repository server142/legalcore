<?php

namespace App\Livewire\Marketing;

use Livewire\Component;
use App\Services\AIService;
use App\Models\MarketingImage;
use Illuminate\Support\Facades\Storage;

class Generator extends Component
{
    public $prompt = '';
    public $style = 'vivid'; // vivid, natural
    public $loading = false;
    public $generatedImage = null; // Última imagen generada
    public $error = null;

    // Ad Mode Fields
    public $is_ad = false; 
    public $headline = '';
    public $subheadline = '';

    protected $rules = [
        'prompt' => 'required|min:5|max:1000',
        'style' => 'required|in:vivid,natural',
        'headline' => 'nullable|string|max:100',
        'subheadline' => 'nullable|string|max:100',
    ];

    public function generate(AIService $ai)
    {
        $this->validate();
        $this->loading = true;
        $this->error = null;

        try {
            // Construcción del Prompt Inteligente
            $finalPrompt = $this->prompt;

            if ($this->is_ad) {
                // Modo Anuncio: Ingeniería de Prompt Refinada para evitar "Mockups"
                $finalPrompt = "Create a high-quality DIGITAL SOCIAL MEDIA ADVERTISEMENT GRAPHIC (Not a photo of a poster). ";
                $finalPrompt .= "Visuals: " . $this->prompt . ". ";
                $finalPrompt .= "FORMAT: Full screen digital graphic design, flat layout. ";
                
                if ($this->headline) {
                    $finalPrompt .= "TEXT INSTRUCTION: Render the title '" . strtoupper($this->headline) . "' in BIG BOLD letters at the top. Spelling must be exact. ";
                }
                
                if ($this->subheadline) {
                    $finalPrompt .= "BOTTOM TEXT: Render '" . $this->subheadline . "' clearly at the bottom. ";
                }
                
                $finalPrompt .= "STYLE: Professional Graphic Design, Corporate Blue tones (if applicable), High Contrast, Sharp Vector-style elements mixed with realistic photos. NO FRAMES, NO WALLS, NO MOCKUPS.";
            }

            // Generar imagen (usando el prompt enriquecido)
            $result = $ai->generateImage($finalPrompt, '1024x1024', $this->style);

            if (!$result['success']) {
                $this->error = $result['error'];
                $this->loading = false;
                return;
            }

            // Guardar registro
            $image = MarketingImage::create([
                'tenant_id' => auth()->user()->tenant_id,
                'user_id' => auth()->id(),
                'prompt' => $finalPrompt, 
                'revised_prompt' => $result['revised_prompt'] ?? null,
                'style' => $this->style,
                'file_path' => $result['path'],
                'provider' => 'dall-e-3',
                'cost' => $result['cost'] ?? 0.040,
            ]);

            $this->generatedImage = $image;
            
            // En modo anuncio, mantenemos el texto para permitir reintentos fáciles
            if (!$this->is_ad) {
                $this->prompt = '';
            }

            $this->dispatch('image-generated'); 

        } catch (\Exception $e) {
            $this->error = 'Ocurrió un error inesperado: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function download($imageId)
    {
        $img = MarketingImage::find($imageId);
        if ($img && $img->tenant_id === auth()->user()->tenant_id) {
            return Storage::disk('public')->download($img->file_path);
        }
    }

    public function render()
    {
        // Obtener historial reciente
        $history = MarketingImage::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();

        return view('livewire.marketing.generator', [
            'history' => $history
        ]);
    }
}
