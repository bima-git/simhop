<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_COACH = 'coach';
    public const ROLE_MAHASISWA = 'mahasiswa';

    // 'role' hanya boleh diisi dari panel Admin (pendaftaran publik ditiadakan).
    protected $fillable = [
        'name', 'email', 'password', 'role', 'nim', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ---------- Helper role ----------

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCoach(): bool
    {
        return $this->role === self::ROLE_COACH;
    }

    public function isMahasiswa(): bool
    {
        return $this->role === self::ROLE_MAHASISWA;
    }

    // ---------- Relasi ----------

    /** Proyek yang diikuti (many-to-many via project_user). */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    public function createdProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function handoverDocuments(): HasMany
    {
        return $this->hasMany(HandoverDocument::class, 'uploaded_by');
    }

    /** Video yang dikirim user ini (Mahasiswa). */
    public function videoSubmissions(): HasMany
    {
        return $this->hasMany(VideoSubmission::class);
    }

    /** Video yang direview user ini (Coach). */
    public function reviewedVideos(): HasMany
    {
        return $this->hasMany(VideoSubmission::class, 'reviewed_by');
    }

    /** Catatan revisi yang ditulis user ini (Coach). */
    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class, 'coach_id');
    }
}