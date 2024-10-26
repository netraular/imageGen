<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Prompt;
use App\Models\LlmResponse;
use App\Models\ThirdPartyService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExecutePromptsChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $prompts;
    protected $executionId;
    protected $userId;

    public function __construct($prompts, $executionId, $userId)
    {
        $this->prompts = $prompts;
        $this->executionId = $executionId;
        $this->userId = $userId;
    }

    public function handle()
    {
        // foreach ($this->prompts as $prompt) {
        //     $llmResponse = LlmResponse::create([
        //         'prompt_id' => $prompt->id,
        //         'execution_id' => $this->executionId,
        //         'status' => 'pending',
        //     ]);

        //     $this->processLlmResponse($llmResponse);
        // }
    }

    private function processLlmResponse(LlmResponse $llmResponse)
    {
        $serviceControl = ThirdPartyService::where('service_name', 'groq_api')->first();

        if ($serviceControl && $serviceControl->isCurrentlyPaused()) {
            $resumeAt = Carbon::parse($serviceControl->resume_at);
            $now = Carbon::now();
            $waitTime = 0;
            if ($resumeAt->isFuture()) {
                $waitTime = $now->diffInSeconds($resumeAt);
            }
            $this->release($waitTime);
            return;
        }

        Log::channel('llmApi')->info('Iniciando generación de respuesta LLM para llmResponse ID: ' . $llmResponse->id);

        $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
        $apiKey = config('services.groq.api_key');

        $client = new \GuzzleHttp\Client();
        $body = [
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $llmResponse->prompt->sentence,
                ],
            ],
            'model' => 'llama-3.1-70b-versatile',
        ];

        try {
            $llmResponse->update(['status' => 'executing']);
            Log::channel('llmApi')->info('Estado actualizado a "executing" para LLM Response ID: ' . $llmResponse->id);

            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (!isset($responseData['choices'][0]['message']['content'])) {
                throw new \Exception('La respuesta de la API no contiene el contenido esperado.');
            }

            $generatedResponse = $responseData['choices'][0]['message']['content'];

            $llmResponse->update([
                'response' => $generatedResponse,
                'status' => 'success',
                'source' => 'Groq API',
            ]);

            Log::channel('llmApi')->info('Respuesta LLM generada y guardada para LLM Response ID: ' . $llmResponse->id);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getCode() === 429) {
                $retryAfter = 60;
                $serviceControl->pause('Rate limit exceeded', ceil($retryAfter / 60));

                Log::channel('llmApi')->warning('Servicio pausado por límite de tasa. Se reintentará en ' . $retryAfter . ' segundos.');

                $this->release($retryAfter);
                return;
            }
            throw $e;
        } catch (\Exception $e) {
            $llmResponse->update(['status' => 'error']);

            Log::channel('llmApi')->error('Error en la generación de respuesta LLM para llmResponse ID: ' . $llmResponse->id . '. Mensaje: ' . $e->getMessage());

            if ($this->attempts() > 3) {
                $this->fail($e);
            } else {
                $this->release(60);
            }
        }
    }
}