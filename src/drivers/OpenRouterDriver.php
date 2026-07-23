<?php

namespace Vendor\Heimdall\Drivers;

use Illuminate\Support\Facades\Http;
use Vendor\Heimdall\DataTransferObjects\PromptRequest;
use Vendor\Heimdall\DataTransferObjects\ProviderResponse;
use Vendor\Heimdall\Exceptions\ProviderException;

class OpenRouterDriver extends BaseDriver
{
    public function send(PromptRequest $request): ProviderResponse
    {
        $model = $request->model ?? $this->config['default_model'];

        $messages = [];
        foreach ($request->systemInstructions as $instruction) {
            $messages[] = ['role' => 'system', 'content' => $instruction];
        }
        $messages[] = ['role' => 'user', 'content' => $request->prompt];

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $request->temperature,
        ];

        if ($request->maxTokens) {
            $payload['max_tokens'] = $request->maxTokens;
        }

        $response = Http::timeout($this->timeout)
            ->withToken($this->config['api_key'])
            ->withHeaders([
                'HTTP-Referer' => $this->config['site_url'],
                'X-Title' => $this->config['site_name'],
                'Content-Type' => 'application/json',
            ])
            ->post('https://openrouter.ai/api/v1/chat/completions', $payload);

        if ($response->failed()) {
            throw new ProviderException("OpenRouter API Error: " . $response->body(), $response->status());
        }

        $data = $response->json();

        return new ProviderResponse(
            text: $data['choices'][0]['message']['content'] ?? '',
            driver: 'openrouter',
            model: $model,
            inputTokens: $data['usage']['prompt_tokens'] ?? 0,
            outputTokens: $data['usage']['completion_tokens'] ?? 0,
            rawPayload: $data
        );
    }
}
