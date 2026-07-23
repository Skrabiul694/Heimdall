<?php

namespace Vendor\Heimdall\Drivers;

use Illuminate\Support\Facades\Http;
use Vendor\Heimdall\DataTransferObjects\PromptRequest;
use Vendor\Heimdall\DataTransferObjects\ProviderResponse;
use Vendor\Heimdall\Exceptions\ProviderException;

class OpenAiCompatibleDriver extends BaseDriver
{
    protected string $driverName;

    public function __construct(array $config, string $driverName)
    {
        parent::__construct($config);
        $this->driverName = $driverName;
    }

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

        $pendingRequest = Http::timeout($this->timeout)
            ->withToken($this->config['api_key'])
            ->withHeaders(['Content-Type' => 'application/json']);

        if (!empty($this->config['organization'])) {
            $pendingRequest->withHeaders(['OpenAI-Organization' => $this->config['organization']]);
        }

        $response = $pendingRequest->post($this->config['base_url'], $payload);

        if ($response->failed()) {
            throw new ProviderException(ucfirst($this->driverName) . " API Error: " . $response->body(), $response->status());
        }

        $data = $response->json();

        return new ProviderResponse(
            text: $data['choices'][0]['message']['content'] ?? '',
            driver: $this->driverName,
            model: $model,
            inputTokens: $data['usage']['prompt_tokens'] ?? 0,
            outputTokens: $data['usage']['completion_tokens'] ?? 0,
            rawPayload: $data
        );
    }
}
