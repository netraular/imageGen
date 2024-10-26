<?php

namespace App\Jobs;


use App\Models\Prompt;
use App\Models\LlmResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Log;

class GenerateLlmResponseBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $llmResponse;

    public function __construct(LlmResponse $llmResponse)
    {
        $this->llmResponse = $llmResponse;
    }

    public function handle()
    {
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
    
            // Actualizar el estado a "ejecutando"
            $this->llmResponse->update([
                'status' => 'executing',
            ]);
    
            Log::channel('llmApi')->info('Estado actualizado a "ejecutando" para LLM Response ID: ' . $this->llmResponse->id);
    
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
            if ($e->getCode() === 401) {
                Log::channel('llmApi')->error('Error 401 Unauthorized: ' . $e->getMessage());
                Log::channel('llmApi')->error('Response body: ' . $e->getResponse()->getBody());
            }
            throw $e;
        } catch (\Exception $e) {
            // Actualizar el estado a "error"
            if ($this->llmResponse) {
                $this->llmResponse->update([
                    'status' => 'error',
                ]);
            }
    
            Log::channel('llmApi')->error('Error en la generación de respuesta LLM para llmResponse ID: ' . $this->llmResponse->id . '. Mensaje: ' . $e->getMessage());
    
            // Manejo de errores
            if ($this->attempts() > 3) {
                $this->fail($e);
            } else {
                $this->release(60); // Reintenta después de 60 segundos
            }
        }
    }
}