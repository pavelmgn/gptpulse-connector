<?php

declare(strict_types=1);

namespace Pavelmgn\GptPulseConnector\Constants;

final class MqRequestType
{
    public const KNOWLEDGE_REQUEST = 1;
    public const BASE_REQUEST      = 2;

    public static function getList(): array
    {
        return [
            self::KNOWLEDGE_REQUEST,
            self::BASE_REQUEST,
        ];
    }

    public static function getListDescription(): array
    {
        return [
            self::KNOWLEDGE_REQUEST => 'Обработка данных для формирования ответа основываясь на данных в БЗ',
            self::BASE_REQUEST      => 'Обработка вопроса и ответа для предоставления базового',
        ];
    }
}
