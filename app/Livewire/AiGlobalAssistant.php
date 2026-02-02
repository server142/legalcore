<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AiChat;
use App\Models\AiChatMessage;
use App\Services\AIService;
use Illuminate\Support\Str;

class AiGlobalAssistant extends Component
{
    public $chats = [];
    public $activeChatId = null;
    public $messages = [];
    public $input = '';
    public $isLoading = false;

    // To auto-scroll
    protected $listeners = ['chatUpdated' => '$refresh'];

    public function mount()
    {
        $this->loadChatsList();
    }

    public function loadChatsList()
    {
        $this->chats = AiChat::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->take(20) // Limit for sidebar efficiency
            ->get();
    }

    public function newChat()
    {
        $this->activeChatId = null;
        $this->messages = [];
        $this->input = '';
    }

    public function loadChat($chatId)
    {
        $chat = AiChat::where('user_id', auth()->id())->find($chatId);
        
        if ($chat) {
            $this->activeChatId = $chat->id;
            $this->messages = $chat->messages()->orderBy('created_at', 'asc')->get()->map(function($msg) {
                return ['role' => $msg->role, 'content' => $msg->content];
            })->toArray();
        }
    }

    public function sendMessage(AIService $aiService)
    {
        $this->validate([
            'input' => 'required|string|max:10000',
        ]);

        $userMessage = $this->input;
        $this->input = ''; // Clear input immediately
        $this->isLoading = true;

        // 1. Create Chat if doesn't exist
        if (!$this->activeChatId) {
            $chat = AiChat::create([
                'user_id' => auth()->id(),
                'title' => Str::limit($userMessage, 30, '...'), // Simple title generation
            ]);
            $this->activeChatId = $chat->id;
            $this->loadChatsList(); // Refresh sidebar
        } else {
            // Security Check: Verify chat ownership
            $chat = AiChat::where('user_id', auth()->id())->find($this->activeChatId);
            if (!$chat) {
                // Potential attack or session mismatch
                $this->newChat();
                return;
            }
        }

        // 2. Save User Message
        AiChatMessage::create([
            'ai_chat_id' => $this->activeChatId,
            'role' => 'user',
            'content' => $userMessage
        ]);

        // Add to local state for instant feedback
        $this->messages[] = ['role' => 'user', 'content' => $userMessage];

        // 3. Call AI Service
        try {
            // Build context (system prompt + recent history)
            $apiMessages = [
                ['role' => 'system', 'content' => "Eres un asistente legal experto y servicial. Tu nombre es 'Diogenes AI'. Responde de forma precisa, profesional y estructurada (uso de markdown)."]
            ];

            // Add recent history to context (last 10 messages)
            $history = array_slice($this->messages, -10);
            foreach ($history as $msg) {
                $apiMessages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }

            // Call API
            // Call API
            $response = $aiService->ask($apiMessages);

            if (isset($response['success']) && $response['success']) {
                $aiContent = $response['content'];
            } else {
                $errorMsg = $response['error'] ?? 'Respuesta desconocida del servicio de IA';
                throw new \Exception($errorMsg);
            }

            // 4. Save Assistant Message
            AiChatMessage::create([
                'ai_chat_id' => $this->activeChatId,
                'role' => 'assistant',
                'content' => $aiContent
            ]);

            $this->messages[] = ['role' => 'assistant', 'content' => $aiContent];
            
            // Update Chat timestamp so it goes to top of list
            AiChat::where('id', $this->activeChatId)->touch();
            $this->loadChatsList();

        } catch (\Exception $e) {
            // Remove the user message from local state if failed? 
            // Better keeps it but maybe mark as error. For now, just show error toast.
            $this->addError('input', 'Error: ' . $e->getMessage());
            
            // Add a system message to chat to inform user visually
            $this->messages[] = ['role' => 'assistant', 'content' => '⚠️ **Error:** ' . $e->getMessage()];
        } finally {
            $this->isLoading = false;
        }
    }

    public function deleteChat($chatId)
    {
        $chat = AiChat::where('user_id', auth()->id())->find($chatId);
        if ($chat) {
            $chat->delete();
            if ($this->activeChatId == $chatId) {
                $this->newChat();
            }
            $this->loadChatsList();
        }
    }

    public function render()
    {
        return view('livewire.ai-global-assistant');
    }
}
