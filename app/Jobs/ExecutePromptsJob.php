<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Prompt;
use App\Models\LlmResponse;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class ExecutePromptsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $templateId;
    protected $batchSize;
    protected $userId;
    protected $executionId;

    public function __construct($templateId, $batchSize = 1000, $userId, $executionId)
    {
        $this->templateId = $templateId;
        $this->batchSize = $batchSize;
        $this->userId = $userId;
        $this->executionId = $executionId;
    }

    public function handle()
    {
        Log::channel('llmApi')->info('Iniciando ejecución de prompts para Template ID: ' . $this->templateId . ' con Execution ID: ' . $this->executionId);

        Prompt::where('template_id', $this->templateId)->chunk($this->batchSize, function ($prompts) {
            ExecutePromptsChunkJob::dispatch($prompts, $this->executionId, $this->userId);
        });

        Log::channel('llmApi')->info('Todos los chunks de prompts encolados para ejecución con Execution ID: ' . $this->executionId);
    }
}