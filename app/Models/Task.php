<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    public const STATUS_TODO = 'todo';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';

    protected $fillable = [
        'project_id', 'created_by', 'assigned_to', 'title', 'description',
        'due_date', 'status', 'progress', 'progress_note',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'progress' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function videoSubmissions(): HasMany
    {
        return $this->hasMany(VideoSubmission::class);
    }

    /** Dipanggil saat video disetujui coach. */
    public function markAsDone(): void
    {
        $this->update([
            'status' => self::STATUS_DONE,
            'progress' => 100,
        ]);
    }
}