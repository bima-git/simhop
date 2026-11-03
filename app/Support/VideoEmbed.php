<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\VideoPlatform;

/**
 * Hasil konversi tautan video mentah menjadi data siap simpan dan putar.
 */
final readonly class VideoEmbed
{
    public function __construct(
        public VideoPlatform $platform,
        public string $videoId,
        public string $embedUrl,
    ) {}
}