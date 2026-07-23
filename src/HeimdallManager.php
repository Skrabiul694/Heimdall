<?php

namespace Vendor\Heimdall;

use Illuminate\Support\Manager;
use Vendor\Heimdall\Drivers\GeminiDriver;
use Vendor\Heimdall\Drivers\OpenRouterDriver;
use Vendor\Heimdall\Drivers\AnthropicDriver;
use Vendor\Heimdall\Drivers\OpenAiCompatibleDriver;
use Vendor\Heimdall\DataTransferObjects\PromptRequest;
use Vendor\Heimdall\DataTransferObjects\ProviderResponse;
use Vendor\Heimdall\Exceptions\AllProvidersFailedException;
use Vendor\Heimdall\Models\LlmLog;

class HeimdallManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('heimdall.default', 'gemini');
    }

    public function createGeminiDriver(): GeminiDriver
    {
        return new GeminiDriver($this->config->get('heimdall.providers.gemini'));
    }

    public function createOpenrouterDriver(): OpenRouterDriver
    {
        return new OpenRouterDriver($this->config->get('heimdall.providers.openrouter'));
    }

    public function createAnthropicDriver(): AnthropicDriver
    {
        return new AnthropicDriver($this->config->get('heimdall.providers.anthropic'));
    }

    public function createOpenaiDriver(): OpenAiCompatibleDriver
    {
        return new OpenAiCompatibleDriver($this->config->get('heimdall.providers.openai'), 'openai');
    }

    public function createDeepseekDriver(): OpenAiCompatibleDriver
    {
        return new OpenAiCompatibleDriver($this->config->get('heimdall.providers.deepseek'), 'deepseek');
    }

    public function createKimiDriver(): OpenAiCompatibleDriver
    {
        return new OpenAiCompatibleDriver($this->config->get('heimdall.providers.kimi'), 'kimi');
    }

    public function executeWithFailover(PromptRequest $request, array $pipeline): ProviderResponse
    {
        foreach ($pipeline as $driverName) {
            $startTime = microtime(true);

            try {
                $response = $this->driver($driverName)->send($request);
                $executionTime = (int) ((microtime(true) - $startTime) * 1000);

                LlmLog::create([
                    'prompt_hash' => hash('sha256', $request->prompt),
                    'driver' => $driverName,
                    'model' => $response->model,
                    'prompt' => $request->prompt,
                    'response_text' => $response->text,
                    'input_tokens' => $response->inputTokens,
                    'output_tokens' => $response->outputTokens,
                    'total_tokens' => $response->totalTokens(),
                    'execution_time_ms' => $executionTime,
                    'is_success' => true,
                    'raw_payload' => $response->rawPayload,
                ]);

                return $response;

            } catch (\Exception $e) {
                $executionTime = (int) ((microtime(true) - $startTime) * 1000);
                report($e);

                LlmLog::create([
                    'prompt_hash' => hash('sha256', $request->prompt),
                    'driver' => $driverName,
                    'model' => $request->model ?? 'default',
                    'prompt' => $request->prompt,
                    'execution_time_ms' => $executionTime,
                    'is_success' => false,
                    'error_message' => $e->getMessage(),
                ]);

                continue;
            }
        }

        throw new AllProvidersFailedException("All configured LLM providers in the chain failed.");
    }
}
