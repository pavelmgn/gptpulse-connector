<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Classes;

use Closure;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Pavelmgn\GptPulseConnector\Contracts\GptPulseConsumerInterface;
use Pavelmgn\GptPulseConnector\Queues\RabbitMQQueue;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer extends Worker implements GptPulseConsumerInterface
{
    private $channel;

    protected $consumerTag;

    protected Closure $process;

    public function setConsumerTag(string $value): void
    {
        $this->consumerTag = $value;
    }

    public function setProcessClosure(Closure $process)
    {
        $this->process = $process;
    }

    public function daemon($connectionName, $queue, WorkerOptions $options)
    {
        /** @var RabbitMQQueue $connection */
        $connection = $this->manager->connection($connectionName);
        $this->channel = $connection->getChannel();

        $connection->declareQueue($queue);

        $this->channel->basic_consume(
            $queue,
            $this->consumerTag,
            true,
            false,
            false,
            false,
            function (AMQPMessage $message) use ($connection, $options, $connectionName, $queue): void {
                $this->processMessage($message->body);
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

    public function processMessage(string $data): void
    {
        $this->process->call($this, $data);
    }
}
