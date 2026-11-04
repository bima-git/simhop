<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskController extends Controller
{
    public function store(TaskRequest $request, Project $project)
    {
        $this->ensureOwner($project);

        $project->tasks()->create([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'due_date' => $request->validated('due_date'),
            'assigned_to' => $request->validated('assigned_to'),
            'created_by' => $request->user()->id,
            'status' => Task::STATUS_TODO,
            'progress' => 0,
        ]);

        return redirect()
            ->route('coach.projects.show', $project)
            ->with('status', 'Tugas berhasil ditambahkan.');
    }

    public function edit(Task $task)
    {
        $project = $task->project;
        $this->ensureOwner($project);

        $memberStudents = $project->members()
            ->where('role', User::ROLE_MAHASISWA)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('coach.tasks.edit', compact('task', 'project', 'memberStudents'));
    }

    public function update(TaskRequest $request, Task $task)
    {
        $project = $task->project;
        $this->ensureOwner($project);

        $task->update([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'due_date' => $request->validated('due_date'),
            'assigned_to' => $request->validated('assigned_to'),
        ]);

        return redirect()
            ->route('coach.projects.show', $project)
            ->with('status', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $task)
    {
        $project = $task->project;
        $this->ensureOwner($project);

        $task->delete();

        return redirect()
            ->route('coach.projects.show', $project)
            ->with('status', 'Tugas berhasil dihapus.');
    }

    private function ensureOwner(Project $project): void
    {
        abort_unless($project->created_by === auth()->id(), 403);
    }
}
