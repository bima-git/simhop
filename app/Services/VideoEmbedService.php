<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\VideoPlatform;
use App\Support\VideoEmbed;
use InvalidArgumentException;

/**
 * Mengonversi tautan video mentah (YouTube / Google Drive) menjadi embed URL
 * yang aman dipakai pada iframe. Hanya host daftar putih yang diterima, dan
 * ID video divalidasi dengan pola ketat.
 */
class VideoEmbedService
{
    private const YOUTUBE_HOSTS = [
        'youtube.com', 'www.youtube.com', 'm.youtube.com',
        'youtube-nocookie.com', 'www.youtube-nocookie.com',
        'youtu.be', 'www.youtu.be',
    ];

    private const DRIVE_HOSTS = ['drive.google.com'];

    /** @throws InvalidArgumentException jika tautan bukan YouTube/Drive yang valid */
    public function convert(string $url): VideoEmbed
    {
        return $this->tryConvert($url)
            ?? throw new InvalidArgumentException(
                'Tautan video tidak valid. Gunakan tautan YouTube atau Google Drive.'
            );
    }

    /** Mengembalikan null jika tautan tidak dikenali. */
    public function tryConvert(string $url): ?VideoEmbed
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        // Toleransi untuk tautan yang ditempel tanpa skema
        if (! preg_match('#^[a-z][a-z0-9+.\-]*://#i', $url)) {
            $url = 'https://'.$url;
        }

        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        if (! in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower($parts['host']);

        return match (true) {
            in_array($host, self::YOUTUBE_HOSTS, true) => $this->fromYouTube($host, $parts),
            in_array($host, self::DRIVE_HOSTS, true) => $this->fromGoogleDrive($parts),
            default => null,
        };
    }

    /**
     * Mendukung watch?v=ID, youtu.be/ID, /embed/ID, /shorts/ID, /live/ID.
     */
    private function fromYouTube(string $host, array $parts): ?VideoEmbed
    {
        $path = trim($parts['path'] ?? '', '/');
        parse_str($parts['query'] ?? '', $query);

        $id = null;

        if (str_ends_with($host, 'youtu.be')) {
            $id = explode('/', $path)[0];
        } elseif ($path === 'watch') {
            $id = $query['v'] ?? null;
        } elseif (preg_match('#^(?:embed|shorts|live|v)/([^/]+)#', $path, $m)) {
            $id = $m[1];
        }

        if (! is_string($id) || ! preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) {
            return null;
        }

        return new VideoEmbed(
            platform: VideoPlatform::YouTube,
            videoId: $id,
            embedUrl: "https://www.youtube-nocookie.com/embed/{$id}",
        );
    }

    /**
     * Mendukung /file/d/ID/view, /file/d/ID/preview, /file/u/0/d/ID/view,
     * open?id=ID, dan uc?id=ID.
     */
    private function fromGoogleDrive(array $parts): ?VideoEmbed
    {
        $path = $parts['path'] ?? '';
        parse_str($parts['query'] ?? '', $query);

        $id = null;

        if (preg_match('#^/file/(?:u/\d+/)?d/([A-Za-z0-9_-]+)(?:/|$)#', $path, $m)) {
            $id = $m[1];
        } elseif (in_array($path, ['/open', '/uc'], true)) {
            $id = $query['id'] ?? null;
        }

        if (! is_string($id) || ! preg_match('/^[A-Za-z0-9_-]{10,}$/', $id)) {
            return null;
        }

        return new VideoEmbed(
            platform: VideoPlatform::GoogleDrive,
            videoId: $id,
            embedUrl: "https://drive.google.com/file/d/{$id}/preview",
        );
    }
}