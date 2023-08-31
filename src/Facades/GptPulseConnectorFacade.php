<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Facades;

use Illuminate\Support\Facades\Facade;
use Pavelmgn\GptPulseConnector\Classes\GptPulseClient;

final class GptPulseConnectorFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return GptPulseClient::class;
    }
}
