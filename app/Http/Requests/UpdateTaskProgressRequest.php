<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');
        if (! $task instanceof Task) {
            $task = Task::find($task);
        }

        if (! $task) {
            return false;
        }

        // Hanya mahasiswa penerima tugas yang boleh mengubah progres, dan tugas tidak boleh berstatus done (BR-24, BR-25)
        return $task->assigned_to === $this->user()?->id && $task->status !== Task::STATUS_DONE;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([Task::STATUS_TODO, Task::STATUS_IN_PROGRESS])],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'progress_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status tugas wajib dipilih.',
            'status.in' => 'Status hanya boleh To Do atau In Progress.',
            'progress.required' => 'Progres tugas wajib diisi.',
            'progress.integer' => 'Progres tugas harus berupa angka.',
            'progress.min' => 'Progres minimal 0%.',
            'progress.max' => 'Progres maksimal 100%.',
            'progress_note.max' => 'Catatan progres maksimal 1000 karakter.',
        ];
    }
}
