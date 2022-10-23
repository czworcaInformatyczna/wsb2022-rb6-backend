<?php

namespace App\Enums;

enum AssetStatus: int
{
    case Archived = 0;
    case Serviced = 25;
    case Ready = 50;
    case HandedOver = 100;

    public static function values(): array
    {
       return array_column(self::cases(), 'value');
    }

}
