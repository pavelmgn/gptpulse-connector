<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Pavelmgn\GptPulseConnector\Classes\Consumer;
use Pavelmgn\GptPulseConnector\Classes\RabbitMqApiClient;
use Pavelmgn\GptPulseConnector\Connectors\RabbitMQConnector;
use Pavelmgn\GptPulseConnector\Console\ConsumeCommand;

final class GptPulseConnectorProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/gptpulse_connector.php' => config_path('gptpulse_connector.php'),
        ]);

        $manager = $this->app['queue'];

        $manager->addConnector('rabbitmq', function () {
            return new RabbitMQConnector($this->app['events']);
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/gptpulse_connector.php',
            'queue.connections.gptpulse',
        );

        if ($this->app->runningInConsole()) {
            $this->app->singleton('gptpulse.consumer', function () {
                $isDownForMaintenance = function () {
                    return $this->app->isDownForMaintenance();
                };

                return new Consumer(
                    $this->app['queue'],
                    $this->app['events'],
                    $this->app[ExceptionHandler::class],
                    $isDownForMaintenance
                );
            });

            $this->app->singleton(ConsumeCommand::class, static function ($app) {
                return new ConsumeCommand(
                    $app['gptpulse.consumer'],
                    $app['cache.store']
                );
            });

            $this->commands([
                ConsumeCommand::class,
            ]);
        }
    }
}
