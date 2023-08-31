<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Providers;

use Illuminate\Support\ServiceProvider;
use Pavelmgn\GptPulseConnector\Classes\GptPulseClient;

final class GptPulseConnectorProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/gptpulse_connector.php' => config_path('gptpulse_connector.php'),
        ]);
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/gptpulse_connector.php', 'gptpulse_connector'
        );

        $this->app->bind(GptPulseClient::class);
    }
}
