<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Rules\ValidVideoUrl;
use Tests\TestCase;

class ValidVideoUrlRuleTest extends TestCase
{
    public function test_passes_with_valid_youtube_url(): void
    {
        $rule = new ValidVideoUrl();
        $failed = false;

        $rule->validate('video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_passes_with_valid_google_drive_url(): void
    {
        $rule = new ValidVideoUrl();
        $failed = false;

        $rule->validate('video_url', 'https://drive.google.com/file/d/1a2b3c4d5e6f7g8h9i0jklmnopqrstuv/view', function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_fails_with_invalid_host(): void
    {
        $rule = new ValidVideoUrl();
        $errorMessage = null;

        $rule->validate('video_url', 'https://vimeo.com/123456789', function ($message) use (&$errorMessage) {
            $errorMessage = $message;
        });

        $this->assertSame('Tautan harus berupa tautan video YouTube atau Google Drive yang valid.', $errorMessage);
    }

    public function test_fails_with_non_string_value(): void
    {
        $rule = new ValidVideoUrl();
        $errorMessage = null;

        $rule->validate('video_url', 12345, function ($message) use (&$errorMessage) {
            $errorMessage = $message;
        });

        $this->assertSame('Tautan harus berupa tautan video YouTube atau Google Drive yang valid.', $errorMessage);
    }

    public function test_fails_with_malformed_url(): void
    {
        $rule = new ValidVideoUrl();
        $errorMessage = null;

        $rule->validate('video_url', 'bukan-url-sama-sekali', function ($message) use (&$errorMessage) {
            $errorMessage = $message;
        });

        $this->assertSame('Tautan harus berupa tautan video YouTube atau Google Drive yang valid.', $errorMessage);
    }
}
