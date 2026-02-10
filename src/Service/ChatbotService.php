<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatbotService
{
    private HttpClientInterface $client;
    private string $apiKey;
    private string $model;

    public function __construct(
        HttpClientInterface $client,
        string $apiKey,
        ?string $model = null
    ) {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->model = $model ?: 'openrouter/auto';
    }

    public function ask(string $message): string
    {
        $message = trim($message);
        if ($message === '') {
            return 'Veuillez écrire un message.';
        }

        if (trim($this->apiKey) === '') {
            return '❌ Clé OpenRouter non trouvée. Configurez OPENROUTER_API_KEY dans .env.local.';
        }

        $response = $this->client->request(
            'POST',
            'https://openrouter.ai/api/v1/chat/completions',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => $_ENV['OPENROUTER_SITE_URL'] ?? 'http://localhost:8000',
                    'X-Title' => $_ENV['OPENROUTER_APP_NAME'] ?? 'BacLab',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                      ['role' => 'system', 'content' => 'Tu es un assistant intelligent intégré dans un site Symfony. Réponds en français avec clarté et concision.'],

                        ['role' => 'user', 'content' => $message],
                    ],
                ],
                'timeout' => 30,
            ]
        );

        $data = $response->toArray(false);
        return $data['choices'][0]['message']['content'] ?? 'لا يوجد رد';
    }
}
