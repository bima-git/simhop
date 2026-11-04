<?php

namespace App\Http\Requests;

use App\Models\VideoSubmission;
use Illuminate\Foundation\Http\FormRequest;

class ReviseVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user || ! $user->isCoach()) {
            return false;
        }

        $video = $this->route('video');
        if (! ($video instanceof VideoSubmission)) {
            $video = VideoSubmission::with('task.project')->find($video);
        }

        return $video !== null
            && $video->task !== null
            && $video->task->project !== null
            && $video->task->project->created_by === $user->id;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => 'Catatan revisi wajib diisi.',
            'note.max' => 'Catatan revisi tidak boleh lebih dari 5000 karakter.',
        ];
    }
}
