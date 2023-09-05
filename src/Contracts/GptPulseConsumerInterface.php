<?php

namespace Pavelmgn\GptPulseConnector\Contracts;

interface GptPulseConsumerInterface
{
    public function processMessage(string $data);
}
