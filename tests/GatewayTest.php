<?php

use Illuminate\Support\Facades\Http;
use Vendor\Heimdall\Facades\Heimdall;
use Vendor\Heimdall\DataTransferObjects\PromptRequest;
use Vendor\Heimdall\DataTransferObjects\ProviderResponse;
use Vendor\Heimdall\Exceptions\AllProvidersFailedException;

uses(\Vendor\Heimdall\Tests\TestCase::class);

it('executes a request successfully via the default driver', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                ['content' => ['parts' => [['text' => 'Hello from Gemini!']]]]
            ],
            'usageMetadata' => ['promptTokenCount' => 10, 'candidatesTokenCount' => 5]
        ], 200)
    ]);

    $request = new PromptRequest(prompt: 'Hello');
    $response = Heimdall::driver('gemini')->send($request);

    expect($response)->toBeInstanceOf(ProviderResponse::class)
        ->and($response->text)->toBe('Hello from Gemini!')
        ->and($response->driver)->toBe('gemini')
        ->and($response->inputTokens)->toBe(10);
});

it('gracefully drops into the fallback chain when primary providers crash', function () {
    Http::fake([
        'api.deepseek.com/*' => Http::response('DeepSeek Down', 500),
        'api.moonshot.ai/*' => Http::response([
            'choices' => [['message' => ['content' => 'Kimi Saved The Day!']]],
            'usage' => ['prompt_tokens' => 15, 'completion_tokens' => 20]
        ], 200)
    ]);

    $request = new PromptRequest(prompt: 'Resilient Prompt');
    
    $response = Heimdall::executeWithFailover($request, ['deepseek', 'kimi']);

    expect($response->text)->toBe('Kimi Saved The Day!')
        ->and($response->driver)->toBe('kimi')
        ->and($response->totalTokens())->toBe(35);
});

it('throws a structural exception when every option in the pipeline fails', function () {
    Http::fake([
        'api.openai.com/*' => Http::response('Unauthorized', 401),
        'api.anthropic.com/*' => Http::response('Rate Limit Exceeded', 429)
    ]);

    $request = new PromptRequest(prompt: 'High Availability Query');

    expect(fn () => Heimdall::executeWithFailover($request, ['openai', 'anthropic']))
        ->toThrow(AllProvidersFailedException::class);
});
