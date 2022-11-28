<?php

namespace App\Enums;
enum LogActionType: string
{
    case Updated = 'updated';
    case Created = 'created';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
