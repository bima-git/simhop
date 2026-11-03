<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'created_by', 'name', 'description', 'status', 'start_date', 'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Semua anggota proyek (many-to-many via project_user). */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /** Hanya anggota ber-role mahasiswa. */
    public function students(): BelongsToMany
    {
        return $this->members()->where('users.role', User::ROLE_MAHASISWA);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function handoverDocuments(): HasMany
    {
        return $this->hasMany(HandoverDocument::class);
    }
}