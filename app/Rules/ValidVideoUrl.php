<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\VideoEmbedService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidVideoUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || app(VideoEmbedService::class)->tryConvert($value) === null) {
            $fail('Tautan harus berupa tautan video YouTube atau Google Drive yang valid.');
        }
    }
}