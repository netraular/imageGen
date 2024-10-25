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
use App\Jobs\GenerateLlmResponseBatchJob;
use App\Notifications\JobCompletedNotification;
use Illuminate\Support\Facades\Auth;

class ExecutePromptsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $templateId;
    protected $batchSize;

    public function __construct($templateId, $batchSize = 1000)
    {
        $this->templateId = $templateId;
        $this->batchSize = $batchSize;
    }

    public function handle()
    {
        // Obtener los prompts en lotes
        Prompt::where('template_id', $this->templateId)->chunk($this->batchSize, function ($prompts) {
            $jobs = [];
            foreach ($prompts as $prompt) {
                // Crear un registro en la tabla llm_responses con el estado "pending"
                $llmResponse = LlmResponse::create([
                    'prompt_id' => $prompt->id,
                    'status' => 'pending',
                ]);

                // Añadir el job a la lista de jobs
                $jobs[] = new GenerateLlmResponseBatchJob($prompt);
            }

            // Crear un batch de jobs y despacharlos
            Bus::batch($jobs)
                ->name('Generate LLM Responses')
                ->finally(function ($batch) {
                    // Enviar notificación de job completado
                    $user = Auth::user();
                    $user->notify(new JobCompletedNotification('ExecutePromptsJob'));
                })
                ->dispatch();
        });
    }
}