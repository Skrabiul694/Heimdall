<?php

namespace Vendor\Heimdall\Drivers;

use Illuminate\Support\Facades\Http;
use Vendor\Heimdall\DataTransferObjects\PromptRequest;
use Vendor\Heimdall\DataTransferObjects\ProviderResponse;
use Vendor\Heimdall\Exceptions\ProviderException;

class GeminiDriver extends BaseDriver
{
    public function send(PromptRequest $request): ProviderResponse
    {
        $model = $request->model ?? $this->config['default_model'];
        $apiKey = $this->config['api_key'];

        $payload = [];
        if (!empty($request->systemInstructions)) {
            $payload['systemInstruction'] = [
                'parts' => array_map(fn($inst) => ['text' => $inst], $request->systemInstructions)
            ];
        }

        $payload['contents'] = [
            [
                'parts' => [
                    ['text' => $request->prompt]
                ]
            ]
        ];

        $payload['generationConfig'] = [
            'temperature' => $request->temperature,
        ];
        if ($request->maxTokens) {
            $payload['generationConfig']['maxOutputTokens'] = $request->maxTokens;
        }

        $response = Http::timeout($this->timeout)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", $payload);

        if ($response->failed()) {
            throw new ProviderException("Gemini API Error: " . $response->body(), $response->status());
        }

        $data = $response->json();

        return new ProviderResponse(
            text: $data['candidates'][0]['content']['parts'][0]['text'] ?? '',
            driver: 'gemini',
            model: $model,
            inputTokens: $data['usageMetadata']['promptTokenCount'] ?? 0,
            outputTokens: $data['usageMetadata']['candidatesTokenCount'] ?? 0,
            rawPayload: $data
        );
    }
}
