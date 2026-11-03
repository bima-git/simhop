<?php

declare(strict_types=1);

namespace App\Enums;

enum VideoPlatform: string
{
    case YouTube = 'youtube';
    case GoogleDrive = 'google_drive';

    public function label(): string
    {
        return match ($this) {
            self::YouTube => 'YouTube',
            self::GoogleDrive => 'Google Drive',
        };
    }
}