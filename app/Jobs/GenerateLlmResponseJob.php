<?php
// Al editar este archivo, ejecutar "sudo service supervisor restart"
namespace App\Jobs;

use App\Models\Prompt;
use App\Models\LlmResponse;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateLlmResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $prompt;

    public function __construct(Prompt $prompt)
    {
        $this->prompt = $prompt;
    }

    public function handle()
    {
        $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
        $apiKey = env('GROQ_API_KEY');

        $client = new Client();
        $body = [
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $this->prompt->sentence,
                ],
            ],
            'model' => 'llama3-8b-8192',
        ];

        try {
            // Buscar la respuesta LLM asociada al prompt
            $llmResponse = LlmResponse::where('prompt_id', $this->prompt->id)->first();

            if (!$llmResponse) {
                // Si no se encuentra ninguna respuesta LLM, lanzar una excepción
                throw new \Exception('No se encontró ninguna respuesta LLM asociada al prompt.');
            }

            // Actualizar el estado a "ejecutando"
            $llmResponse->update([
                'status' => 'executing',
            ]);

            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);

            $responseData = json_decode($response->getBody(), true);
            $generatedResponse = $responseData['choices'][0]['message']['content'] ?? null;

            // Actualizar el estado a "success" y guardar la respuesta
            $llmResponse->update([
                'response' => $generatedResponse,
                'status' => 'success',
                'source' => 'Groq API',
            ]);

        } catch (\Exception $e) {
            // Actualizar el estado a "error"
            if ($llmResponse) {
                $llmResponse->update([
                    'status' => 'error',
                ]);
            }

            // Manejo de errores
            // Puedes reintentar el job después de un tiempo si es necesario
            $this->release(60); // Reintenta después de 60 segundos
        }
    }
}