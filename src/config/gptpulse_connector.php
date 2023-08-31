<?php

return [
    'queue'        => env('GPT_PULSE_CONNECTOR_QUEUE', ''),
    'token'        => env('GPT_PULSE_CONNECTOR_TOKEN', ''),
    'gpt_mq_host'  => env('GPT_PULSE_HOST', ''),
    'gpt_mq_port'  => env('GPT_PULSE_PORT', ''),
    'gpt_mq_user'  => env('GPT_PULSE_USER', ''),
    'gpt_mq_pass'  => env('GPT_PULSE_PASS', ''),
    'gpt_mq_vhost' => env('GPT_PULSE_VHOST', ''),
];
