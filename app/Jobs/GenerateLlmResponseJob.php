<?php

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
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);

            $responseData = json_decode($response->getBody(), true);
            $generatedResponse = $responseData['choices'][0]['message']['content'] ?? '';

            // Guarda la respuesta en la base de datos
            LlmResponse::create([
                'prompt_id' => $this->prompt->id,
                'response' => $generatedResponse,
                'source' => 'Groq API',
            ]);

        } catch (\Exception $e) {
            // Manejo de errores
            // Puedes reintentar el job después de un tiempo si es necesario
            $this->release(60); // Reintenta después de 60 segundos
        }
    }
}