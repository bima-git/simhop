<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->resolveProject();

        return $project !== null && $project->created_by === $this->user()?->id;
    }

    public function rules(): array
    {
        $project = $this->resolveProject();
        $projectId = $project?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'assigned_to' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', User::ROLE_MAHASISWA)->where('is_active', true),
                Rule::exists('project_user', 'user_id')->where('project_id', $projectId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul tugas wajib diisi.',
            'title.max' => 'Judul tugas tidak boleh lebih dari 255 karakter.',
            'due_date.date' => 'Format tenggat waktu tidak valid.',
            'assigned_to.exists' => 'Penerima tugas harus mahasiswa anggota aktif pada proyek ini.',
        ];
    }

    public function resolveProject(): ?Project
    {
        $project = $this->route('project');
        if ($project instanceof Project) {
            return $project;
        }

        if (is_numeric($project)) {
            return Project::find($project);
        }

        $task = $this->route('task');
        if ($task instanceof Task) {
            return $task->project;
        }

        if (is_numeric($task)) {
            return Task::with('project')->find($task)?->project;
        }

        return null;
    }
}
