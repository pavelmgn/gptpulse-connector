<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Classes;

use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Pavelmgn\GptPulseConnector\Queues\RabbitMQQueue;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer extends Worker
{
    private $channel;

    protected $consumerTag;

    public function setConsumerTag(string $value): void
    {
        $this->consumerTag = $value;
    }

    public function daemon($connectionName, $queue, WorkerOptions $options)
    {
        /** @var RabbitMQQueue $connection */
        $connection = $this->manager->connection($connectionName);
        $this->channel = $connection->getChannel();

        echo 'Waiting for new message on channel:1', " \n";
        $this->channel->basic_consume(
            $queue,
            $this->consumerTag,
            true,
            false,
            false,
            false,
            function(AMQPMessage $message) use ($connection, $options, $connectionName, $queue): void {
                echo $message->body;
                $message->ack();
            },
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->stop();
    }

    public function stop($status = 0, $options = null)
    {
        $this->channel->basic_cancel('', false, true);

        return parent::stop($status, $options);
    }
}
