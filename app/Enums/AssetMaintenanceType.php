<?php

namespace App\Enums;

enum AssetMaintenanceType: string
{
    case Repair = 'repair';
    case Clean = 'clean';
    case SoftwareInstalation = 'software_instalation';
    case OsReintall = 'os_reinstall';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
