<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\VideoSubmissionRequest;
use App\Models\Task;
use App\Models\VideoSubmission;

class VideoSubmissionController extends Controller
{
    public function store(VideoSubmissionRequest $request, Task $task)
    {
        $task->videoSubmissions()->create([
            'user_id' => $request->user()->id,
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'video_url' => $request->validated('video_url'),
            'status' => 'pending',
        ]);

        return redirect()
            ->route('mahasiswa.tasks.show', $task)
            ->with('status', 'Video dokumentasi berhasil dikirim.');
    }

    public function update(VideoSubmissionRequest $request, VideoSubmission $video)
    {
        $video->update([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'video_url' => $request->validated('video_url'),
            'status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        return redirect()
            ->route('mahasiswa.tasks.show', $video->task)
            ->with('status', 'Video dokumentasi berhasil dikirim ulang.');
    }
}
