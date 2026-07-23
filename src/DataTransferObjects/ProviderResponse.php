<?php

namespace Vendor\Heimdall\DataTransferObjects;

class ProviderResponse
{
    public function __construct(
        public string $text,
        public string $driver,
        public string $model,
        public int $inputTokens,
        public int $outputTokens,
        public array $rawPayload
    ) {}

    public function totalTokens(): int
    {
        return $this->inputTokens + $this->outputTokens;
    }
}
