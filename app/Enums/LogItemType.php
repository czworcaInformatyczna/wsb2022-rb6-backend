<?php

namespace App\Enums;

use App\Models\Asset;
use App\Models\AssetCategory;

enum LogItemType: string
{
    case Asset = 'asset';
    case AssetCategory = 'asset_category';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getCorrectEntity()
    {
        return match ($this) {
            self::Asset => Asset::class,
            self::AssetCategory => AssetCategory::class,
            default => null
        };
    }
}
