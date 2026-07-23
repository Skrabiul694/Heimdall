<?php

namespace Vendor\Heimdall\Facades;

use Illuminate\Support\Facades\Facade;
use Vendor\Heimdall\DataTransferObjects\PromptRequest;
use Vendor\Heimdall\DataTransferObjects\ProviderResponse;

/**
 * @method static ProviderResponse executeWithFailover(PromptRequest $request, array $pipeline)
 * @method static \Vendor\Heimdall\Contracts\DriverInterface driver(string|null $driver = null)
 * 
 * @see \Vendor\Heimdall\HeimdallManager
 */
class Heimdall extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'heimdall';
    }
}
