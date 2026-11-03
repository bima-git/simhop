<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VideoPlatform;
use App\Services\VideoEmbedService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VideoSubmission extends Model
{
    // 'platform' dan 'embed_url' sengaja tidak masuk $fillable:
    // keduanya diisi otomatis dari video_url pada event saving.
    protected $fillable = [
        'task_id', 'user_id', 'title', 'description', 'video_url',
        'status', 'reviewed_by', 'reviewed_at',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected function casts(): array
    {
        return [
            'platform' => VideoPlatform::class,
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (VideoSubmission $video) {
            if ($video->isDirty('video_url') || blank($video->embed_url)) {
                $embed = app(VideoEmbedService::class)->convert($video->video_url);

                $video->platform = $embed->platform;
                $video->embed_url = $embed->embedUrl;
            }
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class)->oldest();
    }
}