<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviseVideoRequest;
use App\Models\Task;
use App\Models\VideoSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideoReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = VideoSubmission::whereHas('task.project', function ($q) {
            $q->where('created_by', auth()->id());
        });

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $videos = $query->with(['task.project', 'user'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'pending' => VideoSubmission::whereHas('task.project', fn ($q) => $q->where('created_by', auth()->id()))
                ->where('status', 'pending')->count(),
            'revision' => VideoSubmission::whereHas('task.project', fn ($q) => $q->where('created_by', auth()->id()))
                ->where('status', 'revision')->count(),
            'approved' => VideoSubmission::whereHas('task.project', fn ($q) => $q->where('created_by', auth()->id()))
                ->where('status', 'approved')->count(),
        ];

        return view('coach.videos.index', compact('videos', 'status', 'counts'));
    }

    public function show(VideoSubmission $video)
    {
        $this->ensureOwner($video);

        $video->load([
            'task.project',
            'user',
            'reviewer',
            'revisions.coach',
        ]);

        return view('coach.videos.show', compact('video'));
    }

    public function approve(VideoSubmission $video)
    {
        $this->ensureOwner($video);

        abort_if($video->status === 'approved', 422, 'Video sudah disetujui sebelumnya.');

        DB::transaction(function () use ($video) {
            $video->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            $video->task->markAsDone();
        });

        return redirect()
            ->route('coach.videos.show', $video)
            ->with('status', 'Video dokumentasi berhasil disetujui dan tugas telah ditandai selesai.');
    }

    public function revise(ReviseVideoRequest $request, VideoSubmission $video)
    {
        $this->ensureOwner($video);

        abort_if($video->status === 'approved', 422, 'Video yang sudah disetujui tidak dapat diminta revisi.');

        DB::transaction(function () use ($video, $request) {
            $video->revisions()->create([
                'coach_id' => auth()->id(),
                'note' => $request->validated('note'),
            ]);

            $video->update([
                'status' => 'revision',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // Pastikan status tugas tetap/berstatus in_progress (BR-48)
            if ($video->task->status === Task::STATUS_TODO) {
                $video->task->update(['status' => Task::STATUS_IN_PROGRESS]);
            }
        });

        return redirect()
            ->route('coach.videos.show', $video)
            ->with('status', 'Catatan revisi berhasil dikirim ke mahasiswa.');
    }

    private function ensureOwner(VideoSubmission $video): void
    {
        abort_unless(
            $video->task && $video->task->project && $video->task->project->created_by === auth()->id(),
            403
        );
    }
}
