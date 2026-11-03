<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HandoverDocument extends Model
{
    /** Batas ukuran berkas aset pendukung (KB) = 10 MB, dipakai pada validasi 'max'. */
    public const MAX_FILE_KB = 10240;

    public const TYPES = ['guide', 'tech_stack', 'migration', 'asset'];

    protected $fillable = [
        'project_id', 'uploaded_by', 'title', 'type',
        'content', 'file_path', 'file_name', 'file_size',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}