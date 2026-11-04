<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Models\VideoSubmission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $coach = $request->user();

        // 1. Statistik Proyek
        $projectStats = [
            'total' => $coach->createdProjects()->count(),
            'active' => $coach->createdProjects()->where('status', 'active')->count(),
            'completed' => $coach->createdProjects()->where('status', 'completed')->count(),
        ];

        // 2. Statistik Tugas pada Proyek Milik Coach
        $taskStats = [
            'total' => Task::whereHas('project', fn ($q) => $q->where('created_by', $coach->id))->count(),
            'todo' => Task::whereHas('project', fn ($q) => $q->where('created_by', $coach->id))->where('status', Task::STATUS_TODO)->count(),
            'in_progress' => Task::whereHas('project', fn ($q) => $q->where('created_by', $coach->id))->where('status', Task::STATUS_IN_PROGRESS)->count(),
            'done' => Task::whereHas('project', fn ($q) => $q->where('created_by', $coach->id))->where('status', Task::STATUS_DONE)->count(),
        ];

        // 3. Antrean Video Menunggu Review (status = pending)
        $pendingReviewCount = VideoSubmission::whereHas('task.project', fn ($q) => $q->where('created_by', $coach->id))
            ->where('status', 'pending')
            ->count();

        $pendingVideos = VideoSubmission::whereHas('task.project', function ($q) use ($coach) {
                $q->where('created_by', $coach->id);
            })
            ->where('status', 'pending')
            ->with(['task.project', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // 4. Proyek Aktif Terbaru
        $recentProjects = $coach->createdProjects()
            ->withCount(['members', 'tasks'])
            ->latest()
            ->take(5)
            ->get();

        // 5. Total Mahasiswa Bimbingan Aktif (unik)
        $studentCount = User::where('role', User::ROLE_MAHASISWA)
            ->where('is_active', true)
            ->whereHas('projects', fn ($q) => $q->where('created_by', $coach->id))
            ->count();

        return view('coach.dashboard', compact(
            'projectStats',
            'taskStats',
            'pendingReviewCount',
            'pendingVideos',
            'recentProjects',
            'studentCount'
        ));
    }
}
