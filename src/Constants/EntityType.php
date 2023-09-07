<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Constants;

final class EntityType
{
    public const CONSULTATION = 1;
    public const TASK         = 2;

    public static function getList(): array
    {
        return [
            self::CONSULTATION,
            self::TASK,
        ];
    }

    public static function getListDescription(): array
    {
        return [
            self::CONSULTATION => 'Консультация',
            self::TASK         => 'Задания',
        ];
    }
}
