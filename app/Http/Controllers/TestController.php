<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class TestController extends Controller
{
    public function index()
    {
        return view('test.index');
    }

    public function testApi()
    {
        $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
        $apiKey = config('services.groq.api_key');

        $client = new Client();
        $body = [
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Hello, how are you?',
                ],
            ],
            'model' => 'llama3-70b-8192',
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

            return view('test.result', compact('responseData'));

        } catch (ClientException $e) {
            if ($e->getCode() === 429) {
                $retryAfter = 91; // Esperar 61 segundos en caso de error 429
                return view('test.result', ['responseData' => ['error' => 'Servicio pausado por límite de tasa. Se reintentará en ' . $retryAfter . ' segundos.']]);
            }
            throw $e;
        } catch (\Exception $e) {
            return view('test.result', ['responseData' => ['error' => 'Error en la generación de respuesta LLM: ' . $e->getMessage()]]);
        }
    }
}