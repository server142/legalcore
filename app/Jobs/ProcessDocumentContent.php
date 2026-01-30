<?php

namespace App\Jobs;

use App\Models\Documento;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;

class ProcessDocumentContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $documento;

    /**
     * Create a new job instance.
     */
    public function __construct(Documento $documento)
    {
        $this->documento = $documento;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if ($this->documento->extension !== 'pdf') {
                return;
            }

            Log::info("Processing Document ID: {$this->documento->id}");

            $path = storage_path('app/' . $this->documento->path);

            if (!file_exists($path)) {
                $this->documento->update(['processing_status' => 'failed']);
                Log::error("Document file not found at: {$path}");
                return;
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();

            // Sanitize or limit text if needed? 
            // For now, raw text is fine. LongText holds ~4GB.

            $this->documento->update([
                'extracted_text' => $text,
                'processing_status' => 'completed'
            ]);

            Log::info("Document processed successfully.");

        } catch (\Throwable $e) {
            $this->documento->update(['processing_status' => 'failed']);
            Log::error("Failed to process document content: " . $e->getMessage());
        }
    }
}
