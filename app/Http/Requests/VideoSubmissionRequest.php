<?php

namespace App\Http\Requests;

use App\Models\Task;
use App\Models\VideoSubmission;
use App\Rules\ValidVideoUrl;
use Illuminate\Foundation\Http\FormRequest;

class VideoSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user || ! $user->isMahasiswa()) {
            return false;
        }

        // Pengiriman awal: POST mahasiswa/tasks/{task}/video
        if ($this->route('task')) {
            $task = $this->resolveTask();
            if (! $task) {
                return false;
            }

            // Tugas harus ditugaskan ke mahasiswa yang sedang login
            if ($task->assigned_to !== $user->id) {
                return false;
            }

            // Tugas tidak boleh sudah selesai (done)
            if ($task->status === Task::STATUS_DONE) {
                return false;
            }

            // Satu tugas hanya memiliki satu pengiriman video aktif (BR-45, D-07, D-20)
            // Jika sudah ada video, pengiriman berikutnya harus melalui update (resubmit)
            if ($task->videoSubmissions()->exists()) {
                return false;
            }

            return true;
        }

        // Pengiriman ulang (resubmit): PUT mahasiswa/videos/{video}
        if ($this->route('video')) {
            $video = $this->resolveVideo();
            if (! $video) {
                return false;
            }

            // Video harus milik mahasiswa yang sedang login
            if ($video->user_id !== $user->id) {
                return false;
            }

            // Tugas terkait harus ditugaskan ke mahasiswa dan belum done
            $task = $video->task;
            if (! $task || $task->assigned_to !== $user->id || $task->status === Task::STATUS_DONE) {
                return false;
            }

            // Kirim ulang HANYA diperbolehkan saat status 'revision' (D-20, BR-47, BR-49)
            if ($video->status !== 'revision') {
                return false;
            }

            return true;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'video_url' => ['required', 'string', new ValidVideoUrl()],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul video wajib diisi.',
            'title.max' => 'Judul video tidak boleh lebih dari 255 karakter.',
            'video_url.required' => 'Tautan video wajib diisi.',
        ];
    }

    public function resolveTask(): ?Task
    {
        $task = $this->route('task');
        if ($task instanceof Task) {
            return $task;
        }
        if (is_numeric($task)) {
            return Task::find($task);
        }
        return null;
    }

    public function resolveVideo(): ?VideoSubmission
    {
        $video = $this->route('video');
        if ($video instanceof VideoSubmission) {
            return $video;
        }
        if (is_numeric($video)) {
            return VideoSubmission::with('task')->find($video);
        }
        return null;
    }
}
