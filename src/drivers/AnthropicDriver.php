<?php

namespace Vendor\Heimdall\Drivers;

use Illuminate\Support\Facades\Http;
use Vendor\Heimdall\DataTransferObjects\PromptRequest;
use Vendor\Heimdall\DataTransferObjects\ProviderResponse;
use Vendor\Heimdall\Exceptions\ProviderException;

class AnthropicDriver extends BaseDriver
{
    public function send(PromptRequest $request): ProviderResponse
    {
        $model = $request->model ?? $this->config['default_model'];

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $request->prompt]
            ],
            'temperature' => $request->temperature,
            'max_tokens' => $request->maxTokens ?? 4096,
        ];

        if (!empty($request->systemInstructions)) {
            $payload['system'] = implode("\n", $request->systemInstructions);
        }

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'x-api-key' => $this->config['api_key'],
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])
            ->post('https://api.anthropic.com/v1/messages', $payload);

        if ($response->failed()) {
            throw new ProviderException("Anthropic API Error: " . $response->body(), $response->status());
        }

        $data = $response->json();

        return new ProviderResponse(
            text: $data['content'][0]['text'] ?? '',
            driver: 'anthropic',
            model: $model,
            inputTokens: $data['usage']['input_tokens'] ?? 0,
            outputTokens: $data['usage']['output_tokens'] ?? 0,
            rawPayload: $data
        );
    }
}
