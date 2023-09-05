<?php

return [
    'gptpulse_base' => [
        'driver'          => 'rabbitmq',
        'connection'      => 'default',
        'queue'           => env('GPT_PULSE_CONNECTOR_QUEUE', ''),
        'token'           => env('GPT_PULSE_CONNECTOR_TOKEN', ''),
        'host'            => env('GPT_PULSE_HOST', ''),
        'port'            => env('GPT_PULSE_PORT', ''),
        'user'            => env('GPT_PULSE_USER', ''),
        'pass'            => env('GPT_PULSE_PASS', ''),
        'vhost'           => env('GPT_PULSE_VHOST', ''),
        'queue_params'    => [
            'passive'     => env('GPT_PULSE_QUEUE_PASSIVE', false),
            'durable'     => env('GPT_PULSE_QUEUE_DURABLE', true),
            'exclusive'   => env('GPT_PULSE_QUEUE_EXCLUSIVE', false),
            'auto_delete' => env('GPT_PULSE_QUEUE_AUTODELETE', false),
        ],
        'exchange_params' => [
            'name'        => env('GPT_PULSE_EXCHANGE_NAME', null),
            'type'        => env('GPT_PULSE_EXCHANGE_TYPE', 'direct'),
            'passive'     => env('GPT_PULSE_EXCHANGE_PASSIVE', false),
            'durable'     => env('GPT_PULSE_EXCHANGE_DURABLE', true),
            'auto_delete' => env('GPT_PULSE_EXCHANGE_AUTODELETE', false),
        ],
    ],
];
