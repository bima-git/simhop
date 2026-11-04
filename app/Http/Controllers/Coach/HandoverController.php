<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\HandoverDocumentRequest;
use App\Models\HandoverDocument;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class HandoverController extends Controller
{
    public function index(Project $project)
    {
        $this->ensureOwner($project);

        $documents = $project->handoverDocuments()
            ->with('uploader')
            ->orderBy('type')
            ->orderBy('title')
            ->get();

        return view('coach.handover.index', compact('project', 'documents'));
    }

    public function create(Project $project)
    {
        $this->ensureOwner($project);

        return view('coach.handover.create', compact('project'));
    }

    public function store(HandoverDocumentRequest $request, Project $project)
    {
        $this->ensureOwner($project);

        $data = [
            'title' => $request->validated('title'),
            'type' => $request->validated('type'),
            'content' => $request->validated('content'),
            'uploaded_by' => $request->user()->id,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = $file->store('handover/' . $project->id, 'local');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = (int) ceil($file->getSize() / 1024); // KB
        }

        $project->handoverDocuments()->create($data);

        return redirect()
            ->route('coach.handover.index', $project)
            ->with('status', 'Dokumen handover berhasil ditambahkan.');
    }

    public function edit(HandoverDocument $document)
    {
        $this->ensureOwner($document->project);

        return view('coach.handover.edit', compact('document'));
    }

    public function update(HandoverDocumentRequest $request, HandoverDocument $document)
    {
        $project = $document->project;
        $this->ensureOwner($project);

        $data = [
            'title' => $request->validated('title'),
            'type' => $request->validated('type'),
            'content' => $request->validated('content'),
        ];

        if ($request->hasFile('file')) {
            // Hapus berkas lama jika ada
            if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
                Storage::disk('local')->delete($document->file_path);
            }

            $file = $request->file('file');
            $data['file_path'] = $file->store('handover/' . $project->id, 'local');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = (int) ceil($file->getSize() / 1024);
        }

        $document->update($data);

        return redirect()
            ->route('coach.handover.index', $project)
            ->with('status', 'Dokumen handover berhasil diperbarui.');
    }

    public function destroy(HandoverDocument $document)
    {
        $project = $document->project;
        $this->ensureOwner($project);

        // Hapus berkas fisik
        if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('coach.handover.index', $project)
            ->with('status', 'Dokumen handover berhasil dihapus.');
    }

    private function ensureOwner(Project $project): void
    {
        abort_unless($project->created_by === auth()->id(), 403);
    }
}
