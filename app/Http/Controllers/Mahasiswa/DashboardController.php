<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Proyek yang diikuti
        $projects = $user->projects()
            ->with('creator')
            ->withCount('handoverDocuments')
            ->latest()
            ->get();

        // Tugas aktif yang ditugaskan ke mahasiswa (todo & in_progress)
        $activeTasks = $user->assignedTasks()
            ->whereIn('status', [Task::STATUS_TODO, Task::STATUS_IN_PROGRESS])
            ->with(['project', 'videoSubmissions' => fn ($q) => $q->latest()])
            ->orderBy('due_date')
            ->get();

        // Ringkasan status video
        $videoCounts = [
            'pending' => $user->videoSubmissions()->where('status', 'pending')->count(),
            'revision' => $user->videoSubmissions()->where('status', 'revision')->count(),
            'approved' => $user->videoSubmissions()->where('status', 'approved')->count(),
        ];

        return view('mahasiswa.dashboard', compact('projects', 'activeTasks', 'videoCounts'));
    }
}
