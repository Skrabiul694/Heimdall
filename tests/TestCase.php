<?php

namespace Vendor\Heimdall\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Vendor\Heimdall\HeimdallServiceProvider;
use Vendor\Heimdall\Facades\Heimdall;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            HeimdallServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Heimdall' => Heimdall::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('heimdall.providers.gemini.api_key', 'mock-gemini-key');
        $app['config']->set('heimdall.providers.deepseek.api_key', 'mock-deepseek-key');
        $app['config']->set('heimdall.providers.kimi.api_key', 'mock-kimi-key');
    }
}
