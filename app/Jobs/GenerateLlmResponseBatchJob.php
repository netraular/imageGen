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

    protected $prompt;

    public function __construct(Prompt $prompt)
    {
        $this->prompt = $prompt;
    }

    public function handle()
    {
        Log::channel('llmApi')->info('Iniciando generación de respuesta LLM para Prompt ID: ' . $this->prompt->id);
    
        $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
        $apiKey = config('services.groq.api_key');
        
        $client = new Client();
        $body = [
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $this->prompt->sentence,
                ],
            ],
            'model' => 'llama-3.1-70b-versatile',
        ];
    
        try {
            $llmResponse = LlmResponse::firstOrNew(['prompt_id' => $this->prompt->id]); //Parece que se debería hacer a los últimos, no a los primeros que se encuentren. O tener en cuenta el llm_responses en vez de el prompt id.
    
            // Actualizar el estado a "ejecutando"
            $llmResponse->update([
                'status' => 'executing',
            ]);
    
            Log::channel('llmApi')->info('Estado actualizado a "ejecutando" para LLM Response ID: ' . $llmResponse->id);
    
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
            $llmResponse->update([
                'response' => $generatedResponse,
                'status' => 'success',
                'source' => 'Groq API',
            ]);
    
            Log::channel('llmApi')->info('Respuesta LLM generada y guardada para LLM Response ID: ' . $llmResponse->id);
    
        } catch (ClientException $e) {
            if ($e->getCode() === 401) {
                Log::channel('llmApi')->error('Error 401 Unauthorized: ' . $e->getMessage());
                Log::channel('llmApi')->error('Response body: ' . $e->getResponse()->getBody());
            }
            throw $e;
        } catch (\Exception $e) {
            // Actualizar el estado a "error"
            if ($llmResponse) {
                $llmResponse->update([
                    'status' => 'error',
                ]);
            }
    
            Log::channel('llmApi')->error('Error en la generación de respuesta LLM para Prompt ID: ' . $this->prompt->id . '. Mensaje: ' . $e->getMessage());
    
            // Manejo de errores
            if ($this->attempts() > 3) {
                $this->fail($e);
            } else {
                $this->release(60); // Reintenta después de 60 segundos
            }
        }
    }
}