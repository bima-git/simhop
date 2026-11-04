<?php

namespace App\Http\Controllers;

use App\Models\HandoverDocument;
use Illuminate\Support\Facades\Storage;

class HandoverDownloadController extends Controller
{
    public function __invoke(HandoverDocument $document)
    {
        $user = auth()->user();
        $project = $document->project;

        // Coach pemilik proyek ATAU Mahasiswa anggota proyek
        $isOwner = $project->created_by === $user->id;
        $isMember = $project->members()->where('users.id', $user->id)->exists();

        abort_unless($isOwner || $isMember, 403);
        abort_unless($document->file_path && Storage::disk('local')->exists($document->file_path), 404);

        return Storage::disk('local')->download(
            $document->file_path,
            $document->file_name
        );
    }
}
