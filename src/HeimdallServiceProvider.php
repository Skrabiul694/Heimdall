<?php

namespace Vendor\Heimdall;

use Illuminate\Support\ServiceProvider;

class HeimdallServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/heimdall.php', 'heimdall'
        );

        $this->app->singleton('heimdall', function ($app) {
            return new HeimdallManager($app);
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/heimdall.php' => config_path('heimdall.php'),
            ], 'heimdall-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'heimdall-migrations');
        }
    }
}
