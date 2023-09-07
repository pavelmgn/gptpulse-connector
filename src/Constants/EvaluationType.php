<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Constants;

final class EvaluationType
{
    public const SCHOOL_TEXT  = 1;
    public const FROM_ZERO_TO = 2;
    public const IS_COMPLETED = 3;

    public static function getList(): array
    {
        return [
            self::SCHOOL_TEXT,
            self::FROM_ZERO_TO,
            self::IS_COMPLETED,
        ];
    }

    public static function getListDescription(): array
    {
        return [
            self::SCHOOL_TEXT  => 'Оценка школьными баллами',
            self::FROM_ZERO_TO => 'Оценка от нуля до',
            self::IS_COMPLETED => 'Оценка выполнено/не выполнено',
        ];
    }
}
