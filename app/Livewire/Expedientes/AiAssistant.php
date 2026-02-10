<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;
use App\Models\Expediente;
use App\Services\AIService;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class AiAssistant extends Component
{
    public Expediente $expediente;
    
    // UI State
    public $isOpen = false;
    public $mode = 'analyst'; // analyst, drafter, strategist, researcher
    public $messages = [];
    public $input = '';
    public $isLoading = false;
    
    // Document Analysis
    public $documents = [];
    public $selectedDocumentId = null;

    // Services
    protected $aiService;
    protected $ocrService;

    public function boot(AIService $aiService, \App\Services\OcrService $ocrService)
    {
        $this->aiService = $aiService;
        $this->ocrService = $ocrService;
    }

    public function mount(Expediente $expediente)
    {
        $this->expediente = $expediente;
        $this->loadChatHistory();
        $this->loadDocuments();
    }

    public function updatedSelectedDocumentId($value)
    {
        if (empty($value)) return;

        $doc = $this->documents->firstWhere('id', $value);
        if ($doc) {
            $name = $doc->nombre ?? 'el documento';
            $this->messages[] = [
                'role' => 'system',
                'content' => "✅ **Contexto Actualizado**: He leído **{$name}**. \n\nAhora puedes preguntarme cosas como: _¿Qué concluye este documento?_, _¿Hay fechas importantes?_ o _resúmelo_."
            ];
            // Guardar en caché para que persista si recarga
            Cache::put($this->getCacheKey(), $this->messages, now()->addDays(7));
        }
    }

    #[On('document-uploaded')] 
    public function loadDocuments()
    {
        // Solo cargamos PDFs, TXT o Imágenes soportadas por OCR
        $this->documents = $this->expediente->documentos()
            ->whereIn('extension', ['pdf', 'txt', 'jpg', 'jpeg', 'png'])
            ->latest()
            ->get();
    }

    protected function getCacheKey()
    {
        return 'chat_' . $this->expediente->id . '_' . auth()->id();
    }

    protected function loadChatHistory()
    {
        $history = Cache::get($this->getCacheKey(), []);
        
        if (empty($history)) {
            $this->messages = [[
                'role' => 'assistant',
                'content' => 'Hola. Soy tu Asistente Legal (Diogenes). Estoy analizando el expediente ' . $this->expediente->numero . '. ¿En qué puedo apoyarte hoy?'
            ]];
        } else {
            $this->messages = $history;
        }
    }

    public function resetChat()
    {
        Cache::forget($this->getCacheKey());
        $this->messages = [];
        $this->selectedDocumentId = null; // Reset document too
        $this->loadChatHistory();
        $this->dispatch('notify', 'Conversación reiniciada.');
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
        $modeName = match($mode) {
            'analyst' => 'Analista de Casos',
            'drafter' => 'Redactor Jurídico',
            'strategist' => 'Estratega Legal',
            'researcher' => 'Investigador (Jurisprudencia)',
            default => 'Asistente'
        };

        $this->messages[] = [
            'role' => 'system',
            'content' => "Cambiando a modo: **{$modeName}**."
        ];
        
        Cache::put($this->getCacheKey(), $this->messages, now()->addDays(7));
    }

    public function sendMessage()
    {
        $this->validate(['input' => 'required|string']);

        // 1. Add User Message & Clear Input Immediately
        $userMessage = $this->input;
        $this->messages[] = ['role' => 'user', 'content' => $userMessage];
        $this->input = '';
        $this->isLoading = true;

        // Save state immediately
        Cache::put($this->getCacheKey(), $this->messages, now()->addDays(7));

        // 2. Dispatch event to self to trigger AI generation in a separate round-trip
        // This allows the UI to update (clear input) before the heavy AI processing starts
        $this->dispatch('trigger-ai-generation');
    }

    #[On('trigger-ai-generation')]
    public function generateResponse()
    {
        // Build Context
        $context = $this->buildContext();
        
        // Prepare API Messages
        $apiMessages = [
            ['role' => 'system', 'content' => $this->getSystemPrompt()],
            ['role' => 'user', 'content' => "Contexto del Expediente y/o Documentos:\n" . $context],
        ];

        // Append recent chat history
        foreach (array_slice($this->messages, -10) as $msg) {
            if ($msg['role'] !== 'system') { 
                $apiMessages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }
        }

        // Call AI
        $startTime = microtime(true);
        $response = $this->aiService->ask($apiMessages, 0.2);
        $duration = round(microtime(true) - $startTime, 2);

        $this->isLoading = false;

        if (isset($response['success']) && $response['success']) {
            $this->messages[] = [
                'role' => 'assistant', 
                'content' => $response['content'],
                'execution_time' => $duration
            ];
        } else {
            $this->messages[] = ['role' => 'system', 'content' => 'Error: ' . ($response['error'] ?? 'No se pudo conectar con la IA.')];
        }
        
        // Save final state
        Cache::put($this->getCacheKey(), $this->messages, now()->addDays(7));
    }

    private function getSystemPrompt()
    {
        $basePrompt = "Eres DIÓGENES, el Abogado Senior y Estratega Legal de 'LegalCore'. No eres un simple asistente, eres el CEREBRO JURÍDICO del despacho.\n\n" .
            "IDENTIDAD Y TONO:\n" .
            "- Eres técnico, preciso, un poco cínico pero brillante (como un abogado litigante experimentado).\n" .
            "- NUNCA sugieras 'consultar a un abogado'. TÚ eres el abogado. El usuario es tu socio o junior.\n" .
            "- No pidas disculpas innecesarias. Ve al grano.\n" .
            "- Tu objetivo es ganar casos, no solo dar información.\n\n" .

            "FORMATO VISUAL (OBLIGATORIO):\n" .
            "- Usa **negritas** para resaltar: Plazos fatales, Artículos clave, Nombres de partes, y Conclusiones.\n" .
            "- Usa LISTAS (bullets o números) para separar argumentos.\n" .
            "- Usa `### Encabezados` para estructurar respuestas largas.\n" .
            "- Si citas jurisprudencia, usa bloque de cita (>).\n\n" .

            "REGLAS CRÍTICAS DE VERDAD:\n" .
            "1. JAMÁS inventes artículos o jurisprudencia.\n" .
            "2. Si no tienes la ley a la mano, di: '*(Verificar texto vigente)*'.\n" .
            "3. Basa tus argumentos ÚNICAMENTE en los hechos del expediente provisto.\n" .
            "4. Asume jurisdicción Mexicana Federal salvo que se indique lo contrario.\n\n";
        
        // Adjust prompt if document is selected
        if ($this->selectedDocumentId) {
            $basePrompt .= "⚠️ FOCO: El usuario pregunta sobre el DOCUMENTO ADJUNTO. Úsalo como verdad absoluta sobre los hechos recientes. ";
        }

        switch ($this->mode) {
            case 'analyst':
                return $basePrompt . "MODO ANALISTA: Disecciona los hechos. Busca contradicciones. Calcula plazos. Tu respuesta debe parecer un reporte ejecutivo.";
            case 'drafter':
                return $basePrompt . "MODO REDACTOR: Escribe el documento procesal solicitado. Usa lenguaje forense solemne ('Ocurro ante su Señoría', etc). Deja [ESPACIOS] para datos que no tengas.";
            case 'researcher':
                return $basePrompt . "MODO INVESTIGADOR: Busca Tesis Aisladas y Jurisprudencia aplicable. Relaciónala con los hechos del caso.";
            case 'strategist':
                return $basePrompt . "MODO ESTRATEGA: Dime cómo ganar este juicio. Qué pruebas faltan. Qué recursos interponer. Sé agresivo procesalmente.";
            default:
                return $basePrompt;
        }
    }

    private function buildContext()
    {
        // 1. Basic Expediente Info
        $summary = "Expediente: {$this->expediente->numero}\n";
        $summary .= "Título: {$this->expediente->titulo}\n";
        $summary .= "Materia: {$this->expediente->materia}\n";
        $summary .= "Estado: {$this->expediente->estado_procesal}\n\n";
        
        // 2. Document Content (if selected)
        if ($this->selectedDocumentId) {
            $docContent = $this->extractDocumentContent($this->selectedDocumentId);
            
            // ERROR DEBUGGING: If content is an error, show it to the user in the chat
            if ($docContent && str_starts_with($docContent, 'Error')) {
                 $this->messages[] = [
                    'role' => 'system', 
                    'content' => "⚠️ **DIAGNOSTICO TÉCNICO**: " . $docContent
                 ];
                 // Save cache immediately so user sees it even if AI fails next
                 Cache::put($this->getCacheKey(), $this->messages, now()->addDays(7));
            }

            if ($docContent) {
                $summary .= "== CONTENIDO DEL DOCUMENTO SELECCIONADO ==\n";
                $summary .= substr($docContent, 0, 15000); // Limit to ~15k chars to save tokens
                $summary .= "\n== FIN DEL DOCUMENTO ==\n\n";
            } else {
                $summary .= "[Error: No se pudo leer el contenido del documento o es una imagen escaneada].\n\n";
            }
        }

        // 3. Recent Actuaciones
        $summary .= "Últimas Actuaciones:\n";
        foreach ($this->expediente->actuaciones()->latest()->take(5)->get() as $act) {
            $summary .= "- [{$act->fecha->format('d/m/Y')}] {$act->titulo}: {$act->descripcion}\n";
        }

        return $summary;
    }

    private function extractDocumentContent($docId)
    {
        try {
            $doc = $this->documents->firstWhere('id', $docId);
            if (!$doc) return null;

            // 1. Check DB Cache
            if (!empty($doc->extracted_text)) {
                return $doc->extracted_text;
            }

            $finalPath = null;
            // 2. Resolve Path using Storage Facade (disk 'local')
            // This is safer than manual string concatenation
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($doc->path)) {
                $finalPath = \Illuminate\Support\Facades\Storage::disk('local')->path($doc->path);
            } else {
                 // Fallback checks just in case it was stored disjointly
                $possiblePaths = [
                    storage_path('app/' . $doc->path),
                    storage_path('app/public/' . $doc->path),
                    public_path($doc->path),
                    base_path($doc->path),
                    $doc->path
                ];

                foreach ($possiblePaths as $tryPath) {
                    if (file_exists($tryPath) && is_file($tryPath)) {
                        $finalPath = $tryPath;
                        break;
                    }
                }
            }

            if (!$finalPath) {
                return "Error: Documento físico no encontrado. ({$doc->path})";
            }

            // 3. Extract Text using Service (Handles PDF & OCR)
            $text = $this->ocrService->extractText($finalPath);

            // 4. Save to DB Cache if successful
            // Only save if it's meaningful text and not an error message
            if (!empty($text) && strlen($text) > 20 && !str_starts_with($text, 'Error')) {
                // We reload the model from DB to ensure it's updatable
                $dbDoc = \App\Models\Documento::find($doc->id);
                if ($dbDoc) {
                    $dbDoc->extracted_text = $text;
                    $dbDoc->saveQuietly(); // Avoid triggering updated events if unnecessary
                    
                    // Update our local collection too
                    $doc->extracted_text = $text;
                }
            }

            return $text;

        } catch (\Throwable $e) {
            Log::error("Error leyendo documento (AI/OCR): " . $e->getMessage());
            // Show real error for debugging purposes (create a more user-friendly message later)
            return "Error técnico al leer el documento: " . $e->getMessage();
        }
    }
    public function saveAsAiNote($content)
    {
        try {
            \App\Models\AiNote::create([
                'tenant_id' => $this->expediente->tenant_id, 
                'expediente_id' => $this->expediente->id,
                'user_id' => auth()->id(),
                'content' => $content,
            ]);

            $this->js("alert('Guardado exitosamente en Notas de IA.')");
        } catch (\Exception $e) {
            $this->js("alert('Error al guardar: " . addslashes($e->getMessage()) . "')");
        }
    }

    public function exportToWord($content)
    {
        $fileName = 'Diogenes_Draft_' . now()->format('Ymd_His') . '.doc';
        
        return response()->streamDownload(function () use ($content) {
            echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>";
            echo "<head><meta charset='utf-8'><title>Export Header</title></head>";
            echo "<body>";
            echo nl2br($content);
            echo "</body></html>";
        }, $fileName, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    public function exportChatHistory()
    {
        $history = "Historial de Chat - Expediente " . $this->expediente->codigo . "\n";
        $history .= "Fecha: " . now()->format('d/m/Y H:i') . "\n\n";
        
        foreach ($this->messages as $msg) {
            $role = strtoupper($msg['role']);
            $history .= "[{$role}]:\n" . $msg['content'] . "\n\n" . str_repeat('-', 20) . "\n\n";
        }

        $fileName = 'Chat_History_' . now()->format('Ymd_His') . '.txt';

        return response()->streamDownload(function () use ($history) {
            echo $history;
        }, $fileName);
    }

    public function render()
    {
        return view('livewire.expedientes.ai-assistant');
    }
}
