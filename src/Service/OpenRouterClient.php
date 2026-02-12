<?php

namespace App\Service;

class OpenRouterClient
{
    private string $apiKey;
    private string $model;
    private ?string $siteUrl;
    private ?string $appName;

    public function __construct(
        ?string $apiKey,
        ?string $model,
        ?string $siteUrl = null,
        ?string $appName = null
    ) {
        $this->apiKey = (string) $apiKey;
        $this->model = $model ?: 'openrouter/auto';
        $this->siteUrl = $siteUrl;
        $this->appName = $appName;
    }

    public function hasApiKey(): bool
    {
        return trim($this->apiKey) !== '';
    }

    public function chat(string $systemPrompt, string $userMessage): array
    {
        $payload = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'temperature' => 0.7,
        ];

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        if ($this->siteUrl) {
            $headers[] = 'HTTP-Referer: ' . $this->siteUrl;
        }
        if ($this->appName) {
            $headers[] = 'X-Title: ' . $this->appName;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://openrouter.ai/api/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode,
            'raw' => $result,
        ];
    }
}
