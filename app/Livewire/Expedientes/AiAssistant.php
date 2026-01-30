<?php

namespace App\Livewire\Expedientes;

use Livewire\Component;
use App\Models\Expediente;
use App\Services\AIService;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Cache;

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

    public function boot(AIService $aiService)
    {
        $this->aiService = $aiService;
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

    public function loadDocuments()
    {
        // Solo cargamos PDFs o archivos de texto
        $this->documents = $this->expediente->documentos()
            ->whereIn('extension', ['pdf', 'txt'])
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

        // User Message
        $userMessage = $this->input;
        $this->messages[] = ['role' => 'user', 'content' => $userMessage];
        $this->input = '';
        $this->isLoading = true;

        // Save immediately
        Cache::put($this->getCacheKey(), $this->messages, now()->addDays(7));

        // Build Context (Includes Document if selected)
        $context = $this->buildContext();
        
        // Prepare API Messages
        $apiMessages = [
            ['role' => 'system', 'content' => $this->getSystemPrompt()],
            ['role' => 'user', 'content' => "Contexto del Expediente y/o Documentos:\n" . $context],
        ];

        // Append recent chat history (limit to last 10)
        foreach (array_slice($this->messages, -10) as $msg) {
            if ($msg['role'] !== 'system') { 
                $apiMessages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }
        }

        // Call AI
        $response = $this->aiService->ask($apiMessages, 0.2);

        $this->isLoading = false;

        if (isset($response['success']) && $response['success']) {
            $this->messages[] = ['role' => 'assistant', 'content' => $response['content']];
        } else {
            $this->messages[] = ['role' => 'system', 'content' => 'Error: ' . ($response['error'] ?? 'No se pudo conectar con la IA.')];
        }
        
        // Save after AI response
        Cache::put($this->getCacheKey(), $this->messages, now()->addDays(7));
    }

    private function getSystemPrompt()
    {
        $basePrompt = "Eres Diogenes AI, un asistente jurídico. Tu tono es profesional y objetivo. ";
        
        // Adjust prompt if document is selected
        if ($this->selectedDocumentId) {
            $basePrompt .= "El usuario te está preguntando SOBRE EL DOCUMENTO que se ha adjuntado al contexto. Úsalo como tu fuente principal. ";
        }

        switch ($this->mode) {
            case 'analyst':
                return $basePrompt . "Analiza hechos, plazos y estados procesales. Señala inconsistencias.";
            case 'drafter':
                return $basePrompt . "Redacta borradores legales. Usa lenguaje formal forense mexicano.";
            case 'researcher':
            case 'strategist':
                return $basePrompt;
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
            if ($docContent) {
                $summary .= "== CONTENIDO DEL DOCUMENTO SELECCIONADO ==\n";
                $summary .= substr($docContent, 0, 15000); // Limit to ~15k chars to save tokens
                $summary .= "\n== FIN DEL DOCUMENTO ==\n\n";
            } else {
                $summary .= "[Error: No se pudo leer el contenido del documento o es una imagen escaneada].\n\n";
            }
        }

        // 3. Recent Actuaciones (Only if no document selected, or as supplement)
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

            // Check if extracted_text exists in DB first
            if (!empty($doc->extracted_text)) {
                return $doc->extracted_text;
            }

            // Intentar resolver la ruta del archivo en varias ubicaciones posibles
            $possiblePaths = [
                storage_path('app/' . $doc->path),           // Ruta estándar storage/app
                storage_path('app/public/' . $doc->path),    // Ruta pública storage/app/public
                storage_path('app/private/' . $doc->path),   // Ruta privada (Laravel 11+)
                public_path($doc->path),                     // Ruta en carpeta public real
                base_path($doc->path),                       // Ruta relativa a la raíz
                $doc->path                                   // Ruta absoluta almacenada tal cual
            ];

            $finalPath = null;
            foreach ($possiblePaths as $tryPath) {
                if (file_exists($tryPath) && is_file($tryPath)) {
                    $finalPath = $tryPath;
                    break;
                }
            }

            if (!$finalPath) {
                // Debugging info (visible in logs only)
                \Illuminate\Support\Facades\Log::warning("AI Assistant: Archivo no encontrado. Probado: " . implode(', ', $possiblePaths));
                return "Error: El archivo físico no se encuentra en el servidor. (Ruta: {$doc->path})";
            }

            if ($doc->extension === 'pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($finalPath);
                return $pdf->getText();
            } elseif ($doc->extension === 'txt') {
                return file_get_contents($finalPath);
            }
            
            return null;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Error leyendo PDF: " . $e->getMessage());
            return "Error leyendo el archivo. Asegúrese de que sea un PDF de texto y no una imagen escaneada.";
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
