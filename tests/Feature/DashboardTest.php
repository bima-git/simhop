<?php

namespace Tests\Feature;

use App\Models\HandoverDocument;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\VideoSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_user_counts_and_recent_users(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        User::factory()->coach()->create(['name' => 'Coach Budi']);
        User::factory()->mahasiswa()->create(['name' => 'Mahasiswa Ani']);
        User::factory()->inactive()->create(['name' => 'User Nonaktif']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Admin Aktif')
            ->assertSee('Coach Budi')
            ->assertSee('Mahasiswa Ani')
            ->assertSee('User Nonaktif');
    }

    public function test_coach_dashboard_shows_project_task_and_pending_review_metrics(): void
    {
        $coach = User::factory()->coach()->create(['name' => 'Coach Utama']);
        $student = User::factory()->mahasiswa()->create(['name' => 'Mahasiswa Magang']);

        $project = Project::create([
            'name' => 'Proyek SIMHOP',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Tugas Fitur Review',
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => 50,
        ]);

        $video = $task->videoSubmissions()->create([
            'user_id' => $student->id,
            'title' => 'Video Demo Fitur SIMHOP',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($coach)->get(route('coach.dashboard'));

        $response->assertOk()
            ->assertSee('Dashboard Coach')
            ->assertSee('Proyek SIMHOP')
            ->assertSee('Video Demo Fitur SIMHOP')
            ->assertSee('Review Sekarang →');
    }

    public function test_mahasiswa_dashboard_shows_projects_handover_counts_and_active_tasks(): void
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'Proyek Khusus Mahasiswa',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        // Tambah 2 dokumen handover
        $project->handoverDocuments()->create([
            'uploaded_by' => $coach->id,
            'title' => 'Panduan Setup',
            'type' => 'guide',
            'content' => 'Langkah-langkah...',
        ]);
        $project->handoverDocuments()->create([
            'uploaded_by' => $coach->id,
            'title' => 'Tech Stack',
            'type' => 'tech_stack',
            'content' => 'Laravel 12, MySQL',
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Tugas Mahasiswa Aktif',
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => 75,
        ]);

        $response = $this->actingAs($student)->get(route('mahasiswa.dashboard'));

        $response->assertOk()
            ->assertSee('Dashboard Mahasiswa')
            ->assertSee('Proyek Khusus Mahasiswa')
            ->assertSee('2 Dokumen Handover')
            ->assertSee('Buka Dokumen Handover')
            ->assertSee('Tugas Mahasiswa Aktif');
    }

    public function test_dashboard_redirects_each_role_correctly(): void
    {
        $admin = User::factory()->admin()->create();
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        $this->actingAs($admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($coach)->get(route('dashboard'))->assertRedirect(route('coach.dashboard'));
        $this->actingAs($student)->get(route('dashboard'))->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_cross_role_dashboard_access_returns_403(): void
    {
        $admin = User::factory()->admin()->create();
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        // Admin ke coach/mahasiswa
        $this->actingAs($admin)->get(route('coach.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('mahasiswa.dashboard'))->assertForbidden();

        // Coach ke admin/mahasiswa
        $this->actingAs($coach)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($coach)->get(route('mahasiswa.dashboard'))->assertForbidden();

        // Mahasiswa ke admin/coach
        $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($student)->get(route('coach.dashboard'))->assertForbidden();
    }
}
