<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTaskProgressRequest;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = auth()->user()->assignedTasks()
            ->with(['project', 'videoSubmissions' => fn ($q) => $q->latest()])
            ->latest()
            ->paginate(10);

        return view('mahasiswa.tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        abort_unless($task->assigned_to === auth()->id(), 403);

        $task->load(['project.creator', 'project.handoverDocuments', 'videoSubmissions.revisions.coach']);

        return view('mahasiswa.tasks.show', compact('task'));
    }

    public function progress(UpdateTaskProgressRequest $request, Task $task)
    {
        abort_unless($task->assigned_to === auth()->id(), 403);
        abort_if($task->status === Task::STATUS_DONE, 403, 'Tugas yang sudah selesai tidak dapat diubah lagi.');

        $task->update([
            'status' => $request->validated('status'),
            'progress' => $request->validated('progress'),
            'progress_note' => $request->validated('progress_note'),
        ]);

        return redirect()
            ->route('mahasiswa.tasks.show', $task)
            ->with('status', 'Progres tugas berhasil diperbarui.');
    }
}
