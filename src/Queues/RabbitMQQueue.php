<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Queues;

use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue;
use Pavelmgn\GptPulseConnector\Contracts\GptPulseJobInterface;
use Pavelmgn\GptPulseConnector\Jobs\RabbitMQJob;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMQQueue extends Queue implements QueueContract
{
    protected $connection;

    protected $channel;

    protected $defaultQueue;

    protected $configQueue;

    protected $configExchange;

    protected $size = 0;

    public function __construct(AMQPStreamConnection $amqpConnection, $config)
    {
        $this->connection = $amqpConnection;
        $this->defaultQueue = $config['queue'];
        $this->configQueue = $config['queue_params'];
        $this->configExchange = $config['exchange_params'];

        $this->channel = $this->getChannel();
    }

    public function size($queue = null)
    {
        $queue = $this->getQueueName($queue);
        $this->declareQueue($queue);

        return $this->size;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string  $job
     * @param  mixed   $data
     * @param  string  $queue
     *
     * @return bool
     */
    public function push($job, $data = '', $queue = null)
    {
        if (!$job instanceof GptPulseJobInterface) {
            throw (new \AMQPQueueException('Job not implementing GptPulseJobInterface interface'));
        }

        return $this->pushRaw(json_encode($job->getData()), $queue, []);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string  $queue
     * @param  array   $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $queue = $this->getQueueName($queue);
        $this->declareQueue($queue);

        $message = new AMQPMessage($payload, [
            'Content-Type'  => 'application/json',
            'delivery_mode' => 2,
        ]);

        $this->channel->basic_publish($message, 'gptpulse', $queue);

        return true;
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTime|int  $delay
     * @param  string         $job
     * @param  mixed          $data
     * @param  string         $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        if (!$job instanceof GptPulseJobInterface) {
            throw (new \AMQPQueueException('Job not implementing GptPulseJobInterface interface'));
        }

        return $this->pushRaw(json_encode($job->getData()), $queue, ['delay' => $delay]);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string|null  $queue
     *
     * @return \Illuminate\Queue\Jobs\Job|null
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueueName($queue);

        $this->declareQueue($queue);

        $message = $this->channel->basic_get($queue);

        if ($message instanceof AMQPMessage) {
            return new RabbitMQJob($this->container, $this, $this->channel, $queue, $message);
        }

        return null;
    }

    /**
     * @param  string  $queue
     *
     * @return string
     */
    private function getQueueName($queue)
    {
        return $queue ?: $this->defaultQueue;
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel()
    {
        return $this->connection->channel();
    }

    /**
     * @param  string  $name
     */
    private function declareQueue($name)
    {
        $name = $this->getQueueName($name);

        $this->channel->exchange_declare(
            'gptpulse',
            $this->configExchange['type'],
            $this->configExchange['passive'],
            $this->configExchange['durable'],
            $this->configExchange['auto_delete']
        );

        [, $this->size] = $this->channel->queue_declare(
            $name,
            $this->configQueue['passive'],
            $this->configQueue['durable'],
            $this->configQueue['exclusive'],
            $this->configQueue['auto_delete']
        );

        $this->channel->queue_bind($name, 'gptpulse', $name);
    }

    /**
     * @param  string        $destination
     * @param  DateTime|int  $delay
     *
     * @return string
     */
    private function declareDelayedQueue($destination, $delay)
    {
        $destination = $this->getQueueName($destination);
        $name = $this->getQueueName($destination) . '_deferred_' . $delay;

        $this->channel->exchange_declare(
            'gptpulse',
            $this->configExchange['type'],
            $this->configExchange['passive'],
            $this->configExchange['durable'],
            $this->configExchange['auto_delete']
        );

        $this->channel->queue_declare(
            $name,
            $this->configQueue['passive'],
            $this->configQueue['durable'],
            $this->configQueue['exclusive'],
            $this->configQueue['auto_delete'],
            false,
            new AMQPTable([
                'x-dead-letter-exchange'    => $destination,
                'x-dead-letter-routing-key' => $destination,
                'x-message-ttl'             => $delay * 1000,
            ])
        );

        $this->channel->queue_bind($name, $name, $name);

        return $name;
    }
}
