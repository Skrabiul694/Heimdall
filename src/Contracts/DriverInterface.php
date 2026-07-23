<?php

namespace Vendor\Heimdall\Contracts;

use Vendor\Heimdall\DataTransferObjects\PromptRequest;
use Vendor\Heimdall\DataTransferObjects\ProviderResponse;

interface DriverInterface
{
    public function send(PromptRequest $request): ProviderResponse;
}
