<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withCount(['members', 'tasks'])
            ->where('created_by', auth()->id())
            ->latest()
            ->paginate(10);

        return view('coach.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('coach.projects.create');
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('coach.projects.show', $project)
            ->with('status', 'Proyek berhasil dibuat.');
    }

    public function show(Project $project)
    {
        $this->ensureOwner($project);

        $project->load('members');

        // Mahasiswa aktif yang belum menjadi anggota proyek ini
        $students = User::where('role', User::ROLE_MAHASISWA)
            ->where('is_active', true)
            ->whereNotIn('id', $project->members->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('coach.projects.show', compact('project', 'students'));
    }

    public function addMember(Request $request, Project $project)
    {
        $this->ensureOwner($project);

        $data = $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->where('role', User::ROLE_MAHASISWA)
                    ->where('is_active', true),
            ],
        ]);

        $project->members()->syncWithoutDetaching([$data['user_id']]);

        return back()->with('status', 'Mahasiswa ditambahkan ke proyek.');
    }

    public function removeMember(Project $project, User $user)
    {
        $this->ensureOwner($project);

        $project->members()->detach($user->id);

        return back()->with('status', 'Mahasiswa dikeluarkan dari proyek.');
    }

    /** Coach hanya boleh mengelola proyek miliknya sendiri. */
    private function ensureOwner(Project $project): void
    {
        abort_unless($project->created_by === auth()->id(), 403);
    }
}