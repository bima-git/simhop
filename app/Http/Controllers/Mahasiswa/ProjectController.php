<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjectController extends Controller
{
    public function show(Project $project)
    {
        // Mahasiswa hanya bisa melihat proyek yang diikutinya
        abort_unless(
            $project->members()->where('users.id', auth()->id())->exists(),
            403
        );

        $project->load([
            'creator',
            'handoverDocuments' => fn ($q) => $q->orderBy('type')->orderBy('title'),
        ]);

        return view('mahasiswa.projects.show', compact('project'));
    }
}
