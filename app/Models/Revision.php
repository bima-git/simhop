<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revision extends Model
{
    protected $fillable = ['video_submission_id', 'coach_id', 'note'];

    public function videoSubmission(): BelongsTo
    {
        return $this->belongsTo(VideoSubmission::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }
}