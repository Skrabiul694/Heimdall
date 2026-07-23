<?php

namespace Vendor\Heimdall\DataTransferObjects;

class PromptRequest
{
    public function __construct(
        public string $prompt,
        public ?string $model = null,
        public float $temperature = 0.7,
        public ?int $maxTokens = null,
        public array $systemInstructions = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            prompt: $data['prompt'],
            model: $data['model'] ?? null,
            temperature: $data['temperature'] ?? 0.7,
            maxTokens: $data['max_tokens'] ?? null,
            systemInstructions: $data['system_instructions'] ?? []
        );
    }
}
