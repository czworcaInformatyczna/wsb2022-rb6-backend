<?php

namespace App\Enums;

use App\Models\Asset;

enum LogItemType: string
{
    case Asset = 'asset';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getCorrectEntity()
    {
        return match ($this) {
            self::Asset => Asset::class,
            default => null
        };
    }
}
