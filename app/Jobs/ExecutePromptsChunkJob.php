<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\LlmResponse;
use App\Models\ThirdPartyService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Bus\Batchable;

class ExecutePromptsChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $llmResponses;
    protected $executionId;
    protected $userId;

    public function __construct($llmResponses, $executionId, $userId)
    {
        $this->llmResponses = $llmResponses;
        $this->executionId = $executionId;
        $this->userId = $userId;
    }

    public function handle()
    {
        Log::channel('llmApi')->info("Ejecutando nuevo ChunkJob");

        foreach ($this->llmResponses as $llmResponse) {
            $this->waitIfServicePaused();
            $this->processLlmResponse($llmResponse);
        }
    }

    private function waitIfServicePaused()
    {
        $serviceControl = ThirdPartyService::where('service_name', 'groq_api')->first();
      
        // Si el servicio está pausado, espera hasta que esté disponible
        while ($serviceControl && $serviceControl->isCurrentlyPaused()) {
            $now = Carbon::now();
            $resumeAt = Carbon::parse($serviceControl->resume_at);
            if ($resumeAt->greaterThan($now)) {
                $waitTime = $now->diffInSeconds($resumeAt);
                Log::channel('llmApi')->info("Servicio pausado, esperando {$waitTime} segundos para continuar.");
                sleep($waitTime+1);
            }else{
                $serviceControl->resume(); // Actualizar el estado de pausa
                Log::channel('llmApi')->info("Servicio reanudado.");
            }
        }
    }

    private function processLlmResponse(LlmResponse $llmResponse)
    {
        $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
        $apiKey = config('services.groq.api_key');

        $client = new Client();
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
            Log::channel('llmApi')->info("Estado actualizado a 'executing' para LLM Response ID: {$llmResponse->id}");

            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);

            $responseData = json_decode($response->getBody(), true);

            // Verificar si el contenido esperado está en la respuesta
            if (!isset($responseData['choices'][0]['message']['content'])) {
                throw new \Exception('La respuesta de la API no contiene el contenido esperado.');
            }

            // Guardar la respuesta generada
            $generatedResponse = $responseData['choices'][0]['message']['content'];
            $llmResponse->update([
                'response' => $generatedResponse,
                'status' => 'success',
                'source' => 'Groq API',
            ]);

            Log::channel('llmApi')->info("Respuesta LLM generada y guardada para LLM Response ID: {$llmResponse->id}");

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getCode() === 429) {
                $retryAfter = 61; // Esperar 61 segundos en caso de error 429
                $serviceControl = ThirdPartyService::where('service_name', 'groq_api')->first();
                if ($serviceControl) {
                    $serviceControl->pause('Rate limit exceeded',$retryAfter);
                    Log::channel('llmApi')->warning("Servicio pausado por límite de tasa. Se reintentará en {$retryAfter} segundos.");
                }
                $this->release($retryAfter);
                return;
            }
            throw $e;
        } catch (\Exception $e) {
            $llmResponse->update(['status' => 'error']);
            Log::channel('llmApi')->error("Error en la generación de respuesta LLM para LLM Response ID: {$llmResponse->id}. Mensaje: {$e->getMessage()}");

            if ($this->attempts() > 3) {
                $this->fail($e); // Falla el job si ya ha habido más de tres intentos
            } else {
                $this->release(61); // Espera 60 segundos antes de reintentar en caso de error
            }
        }
    }
}