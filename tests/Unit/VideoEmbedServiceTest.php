<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\VideoPlatform;
use App\Services\VideoEmbedService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VideoEmbedServiceTest extends TestCase
{
    private VideoEmbedService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VideoEmbedService();
    }

    #[DataProvider('validUrlProvider')]
    public function test_converts_valid_urls(string $url, VideoPlatform $expectedPlatform, string $expectedId, string $expectedEmbed): void
    {
        $result = $this->service->convert($url);

        $this->assertSame($expectedPlatform, $result->platform);
        $this->assertSame($expectedId, $result->videoId);
        $this->assertSame($expectedEmbed, $result->embedUrl);
    }

    public static function validUrlProvider(): array
    {
        return [
            // 1. YouTube standard watch
            'yt_watch' => [
                'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                VideoPlatform::YouTube,
                'dQw4w9WgXcQ',
                'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            ],
            // 2. YouTube without www
            'yt_no_www' => [
                'https://youtube.com/watch?v=dQw4w9WgXcQ',
                VideoPlatform::YouTube,
                'dQw4w9WgXcQ',
                'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            ],
            // 3. YouTube mobile
            'yt_mobile' => [
                'https://m.youtube.com/watch?v=dQw4w9WgXcQ',
                VideoPlatform::YouTube,
                'dQw4w9WgXcQ',
                'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            ],
            // 4. YouTube shortlink
            'yt_shortlink' => [
                'https://youtu.be/dQw4w9WgXcQ',
                VideoPlatform::YouTube,
                'dQw4w9WgXcQ',
                'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            ],
            // 5. YouTube embed
            'yt_embed' => [
                'https://www.youtube.com/embed/dQw4w9WgXcQ',
                VideoPlatform::YouTube,
                'dQw4w9WgXcQ',
                'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            ],
            // 6. YouTube shorts
            'yt_shorts' => [
                'https://www.youtube.com/shorts/dQw4w9WgXcQ',
                VideoPlatform::YouTube,
                'dQw4w9WgXcQ',
                'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            ],
            // 7. YouTube without scheme
            'yt_no_scheme' => [
                'youtu.be/dQw4w9WgXcQ',
                VideoPlatform::YouTube,
                'dQw4w9WgXcQ',
                'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            ],
            // 8. Drive view
            'drive_view' => [
                'https://drive.google.com/file/d/1a2B3c4D5e6F7g8H9i0J_kLmNo/view',
                VideoPlatform::GoogleDrive,
                '1a2B3c4D5e6F7g8H9i0J_kLmNo',
                'https://drive.google.com/file/d/1a2B3c4D5e6F7g8H9i0J_kLmNo/preview',
            ],
            // 9. Drive multi-account
            'drive_multi_account' => [
                'https://drive.google.com/file/u/0/d/1a2B3c4D5e6F7g8H9i0J_kLmNo/view',
                VideoPlatform::GoogleDrive,
                '1a2B3c4D5e6F7g8H9i0J_kLmNo',
                'https://drive.google.com/file/d/1a2B3c4D5e6F7g8H9i0J_kLmNo/preview',
            ],
            // 10. Drive open
            'drive_open' => [
                'https://drive.google.com/open?id=1a2B3c4D5e6F7g8H9i0J_kLmNo',
                VideoPlatform::GoogleDrive,
                '1a2B3c4D5e6F7g8H9i0J_kLmNo',
                'https://drive.google.com/file/d/1a2B3c4D5e6F7g8H9i0J_kLmNo/preview',
            ],
            // 11. Drive uc
            'drive_uc' => [
                'https://drive.google.com/uc?id=1a2B3c4D5e6F7g8H9i0J_kLmNo',
                VideoPlatform::GoogleDrive,
                '1a2B3c4D5e6F7g8H9i0J_kLmNo',
                'https://drive.google.com/file/d/1a2B3c4D5e6F7g8H9i0J_kLmNo/preview',
            ],
        ];
    }

    #[DataProvider('invalidUrlProvider')]
    public function test_rejects_invalid_urls(string $url): void
    {
        $this->assertNull($this->service->tryConvert($url));

        $this->expectException(InvalidArgumentException::class);
        $this->service->convert($url);
    }

    public static function invalidUrlProvider(): array
    {
        return [
            // 1. Phishing / similar host
            'phishing_youtube' => ['https://notyoutube.com/watch?v=dQw4w9WgXcQ'],
            // 2. Phishing / similar drive
            'phishing_drive' => ['https://notdrive.google.com/file/d/1a2B3c4D5e6F7g8H9i0J_kLmNo/view'],
            // 3. Scheme javascript
            'javascript_scheme' => ['javascript:alert(1)'],
            // 4. Short ID youtube
            'short_id_youtube' => ['https://youtu.be/short'],
            // 5. Short ID drive (<10 chars)
            'short_id_drive' => ['https://drive.google.com/file/d/short/view'],
            // 6. Drive folder
            'drive_folder' => ['https://drive.google.com/drive/folders/1a2B3c4D5e6F7g8H9i0J_kLmNo'],
            // 7. Array query param
            'array_param' => ['https://www.youtube.com/watch?v[]=dQw4w9WgXcQ'],
            // 8. Empty string
            'empty_string' => [''],
            // 9. Other service
            'vimeo' => ['https://vimeo.com/12345678'],
            // 10. Malformed URL
            'malformed_url' => ['http://'],
        ];
    }
}
