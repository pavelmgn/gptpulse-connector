<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Connectors;

use Exception;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Pavelmgn\GptPulseConnector\Queues\RabbitMQQueue;
use PhpAmqpLib\Connection\AMQPStreamConnection;

final class RabbitMQConnector implements ConnectorInterface
{
    protected $connection;

    /**
     * @throws Exception
     */
    public function connect(array $config)
    {
        $this->connection = new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['pass'],
            $config['vhost']
        );

        return new RabbitMQQueue(
            $this->connection,
            $config
        );
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
