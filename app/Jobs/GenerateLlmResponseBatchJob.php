<?php

namespace App\Jobs;

use App\Models\LlmResponse;
use App\Models\ThirdPartyService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GenerateLlmResponseBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $llmResponse;

    
    public function __construct(LlmResponse $llmResponse)
    {
        $this->llmResponse = $llmResponse;
        $this->onQueue('llmApi');
    }

    public function handle()
    {
        // Obtener el registro de control del servicio de Groq API
        $serviceControl = ThirdPartyService::where('service_name', 'groq_api')->first();

    // Verificar si el servicio está en pausa
    if ($serviceControl && $serviceControl->isCurrentlyPaused()) {
        $resumeAt = Carbon::parse($serviceControl->resume_at);
        $now = Carbon::now();
        $waitTime = 0;
        if ($resumeAt->isFuture()) {
            // Calcula la diferencia en segundos solo si es futuro
            $waitTime = $now->diffInSeconds($resumeAt);

        }
        $this->release( $waitTime);
        return;
    }



        Log::channel('llmApi')->info('Iniciando generación de respuesta LLM para llmResponse ID: ' . $this->llmResponse->id);
        
        $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
        $apiKey = config('services.groq.api_key');
        
        $client = new Client();
        $body = [
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $this->llmResponse->prompt->sentence,
                ],
            ],
            'model' => 'llama-3.1-70b-versatile',
        ];
    
        try {
            // Actualizar el estado a "executing"
            $this->llmResponse->update(['status' => 'executing']);
            Log::channel(channel: 'llmApi')->info('Estado actualizado a "executing" para LLM Response ID: ' . $this->llmResponse->id);
            
            // Realizar la solicitud a la API
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
    
            // Actualizar el estado a "success" y guardar la respuesta
            $this->llmResponse->update([
                'response' => $generatedResponse,
                'status' => 'success',
                'source' => 'Groq API',
            ]);
    
            Log::channel('llmApi')->info('Respuesta LLM generada y guardada para LLM Response ID: ' . $this->llmResponse->id);
    
        } catch (ClientException $e) {
            if ($e->getCode() === 429) {
                // // Si se recibe un error 429 (límite de tasa), pausamos el servicio
                $retryAfter =  60;  //$e->getResponse()->getHeader('Retry-After')[0] ?? 60;
                $serviceControl->pause('Rate limit exceeded', ceil($retryAfter / 60));
                
                Log::channel('llmApi')->warning('Servicio pausado por límite de tasa. Se reintentará en ' . $retryAfter . ' segundos.');

                // // Liberar el job hasta el tiempo de espera indicado
                $this->release($retryAfter);

                return;
            }
            throw $e;
        } catch (\Exception $e) {
            // Actualizar el estado a "error"
            $this->llmResponse->update(['status' => 'error']);
    
            Log::channel('llmApi')->error('Error en la generación de respuesta LLM para llmResponse ID: ' . $this->llmResponse->id . '. Mensaje: ' . $e->getMessage());
    
            // Manejo de errores con reintentos
            if ($this->attempts() > 3) {
                $this->fail($e);
            } else {
                $this->release(60); // Reintentar después de 60 segundos
            }
        }
    }
}
