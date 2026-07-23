<?php

namespace Vendor\Heimdall\Drivers;

use Vendor\Heimdall\Contracts\DriverInterface;

abstract class BaseDriver implements DriverInterface
{
    protected array $config;
    protected int $timeout;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->timeout = config('heimdall.timeout', 30);
    }
}
