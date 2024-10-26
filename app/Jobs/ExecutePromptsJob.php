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
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ExecutePromptsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $templateId;
    protected $batchSize;
    protected $userId;

    public function __construct($templateId, $batchSize = 1000, $userId)
    {
        $this->templateId = $templateId;
        $this->batchSize = $batchSize;
        $this->userId = $userId;
    }

    public function handle()
    {

        // Obtener el último execution_id para este template
        $lastExecutionId = LlmResponse::whereHas('prompt', function ($query) {
            $query->where('template_id', $this->templateId);
        })->max('execution_id') ?? 0;

        $executionId = $lastExecutionId + 1;
        
        Log::channel('llmApi')->info('Iniciando ejecución de prompts para Template ID: ' . $this->templateId. ' con Execution ID: ' . $executionId);

        // Obtener los prompts en lotes
        Prompt::where('template_id', $this->templateId)->chunk($this->batchSize, function ($prompts) use ($executionId) {
            $jobs = [];
            foreach ($prompts as $prompt) {

                // Crear un registro en la tabla llm_responses con el estado "pending"
                $llmResponse = LlmResponse::create([
                    'prompt_id' => $prompt->id,
                    'execution_id' => $executionId,
                    'status' => 'pending',
                ]);

                // Añadir el job a la lista de jobs
                $jobs[] = new GenerateLlmResponseBatchJob($llmResponse);
                Log::channel('llmApi')->info('Nuevo registro en llm_responses creado para Prompt ID: ' . $prompt->id . ' con Execution ID: ' . $executionId);
            }

            // Crear un batch de jobs y despacharlos
            Bus::batch($jobs)
                ->name('Generate LLM Responses')
                // ->finally(function ($batch) {
                //     // Recuperar el usuario usando el ID almacenado
                //     $user = User::find($this->userId);

                //     // Enviar notificación de job completado
                //     $user->notify(new JobCompletedNotification('ExecutePromptsJob'));
                // Log::channel('llmApi')->info('Batch de jobs completado para Template ID: ' . $this->templateId . ' con Execution ID: ' . $executionId);
                // })
                ->dispatch();
        });
    }
}