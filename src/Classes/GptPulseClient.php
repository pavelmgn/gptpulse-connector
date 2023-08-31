<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Classes;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

final class GptPulseClient
{
    /**
     * @throws Exception
     */
    public function publish($message)
    {
        $connection = new AMQPStreamConnection(
            config('gptpulse_connector.gpt_mq_host'),
            config('gptpulse_connector.gpt_mq_port'),
            config('gptpulse_connector.gpt_mq_user'),
            config('gptpulse_connector.gpt_mq_pass'),
            config('gptpulse_connector.gpt_mq_vhost'),
        );
        $channel = $connection->channel();
        $channel->exchange_declare('test_exchange', 'direct', false, false, false);
        $channel->queue_declare(config('gptpulse_connector.queue'), false, false, false, false);
        $channel->queue_bind(config('gptpulse_connector.queue'), 'test_exchange', 'test_key');
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, 'test_exchange', 'test_key');
        echo " [x] Sent $message to test_exchange / test_queue.\n";
        $channel->close();
        $connection->close();
    }

    /**
     * @throws Exception
     */
    public function consume()
    {
        $connection = new AMQPStreamConnection(
            config('gptpulse_connector.gpt_mq_host'),
            config('gptpulse_connector.gpt_mq_port'),
            config('gptpulse_connector.gpt_mq_user'),
            config('gptpulse_connector.gpt_mq_pass'),
            config('gptpulse_connector.gpt_mq_vhost'),
        );
        $channel = $connection->channel();
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
        };
        $channel->queue_declare(config('gptpulse_connector.queue'), false, false, false, false);
        $channel->basic_consume(config('gptpulse_connector.queue'), '', false, true, false, false, $callback);
        echo 'Waiting for new message on test_queue', " \n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
