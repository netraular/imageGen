<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Prompt;
use App\Models\LlmResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use Carbon\Carbon;

class ExecutePromptsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $templateId;
    protected $batchSize;
    protected $userId;
    protected $executionId;

    public function __construct($templateId, $batchSize, $userId, $executionId)
    {
        $this->templateId = $templateId;
        $this->batchSize = $batchSize;
        $this->userId = $userId;
        $this->executionId = $executionId;
    }

    public function handle()
    {
        Log::channel('llmApi')->info("Iniciando encolado de chunks para Template ID: {$this->templateId} con Execution ID: {$this->executionId}");

        $prompts = Prompt::where('template_id', $this->templateId)->get();

        // Generar las instancias de LlmResponse
        $llmResponses = $prompts->map(function ($prompt) {
            return LlmResponse::create([
                'prompt_id' => $prompt->id,
                'execution_id' => $this->executionId,
                'status' => 'pending',
            ]);
        });

        $chunks = $llmResponses->chunk($this->batchSize);

        $jobs = $chunks->map(function ($chunk, $index) {
            return (new ExecutePromptsChunkJob($chunk, $this->executionId, $this->userId));
        });

        Bus::batch($jobs)
            ->name('Generate LLM Responses')
            ->onQueue('llmApiChunk')
            ->allowFailures()
            ->dispatch();

        Log::channel('llmApi')->info("Todos los chunks encolados para Execution ID: {$this->executionId}");
    }
}