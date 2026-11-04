<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\VideoSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_can_view_dashboard_with_projects_and_active_tasks(): void
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'SIMHOP Laravel Project',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $activeTask = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Buat Fitur Monitoring',
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => 30,
        ]);

        $response = $this->actingAs($student)->get(route('mahasiswa.dashboard'));

        $response->assertOk();
        $response->assertSee('SIMHOP Laravel Project');
        $response->assertSee('Buat Fitur Monitoring');
        $response->assertSee('30%');
    }

    public function test_mahasiswa_can_view_only_their_own_tasks_in_index(): void
    {
        $coach = User::factory()->coach()->create();
        $studentA = User::factory()->mahasiswa()->create(['name' => 'Mahasiswa A']);
        $studentB = User::factory()->mahasiswa()->create(['name' => 'Mahasiswa B']);

        $project = Project::create([
            'name' => 'Proyek Bersama',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach([$studentA->id, $studentB->id]);

        $taskA = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $studentA->id,
            'title' => 'Tugas Mahasiswa A',
            'status' => Task::STATUS_TODO,
        ]);

        $taskB = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $studentB->id,
            'title' => 'Tugas Mahasiswa B',
            'status' => Task::STATUS_TODO,
        ]);

        $response = $this->actingAs($studentA)->get(route('mahasiswa.tasks.index'));

        $response->assertOk();
        $response->assertSee('Tugas Mahasiswa A');
        $response->assertDontSee('Tugas Mahasiswa B');
    }

    public function test_mahasiswa_can_view_detail_of_their_own_task(): void
    {
        $coach = User::factory()->coach()->create(['name' => 'Pak Coach']);
        $student = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'Proyek Alpha',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Desain ERD Database',
            'description' => 'Gunakan skema 7 tabel',
            'status' => Task::STATUS_TODO,
            'progress' => 0,
        ]);

        $response = $this->actingAs($student)->get(route('mahasiswa.tasks.show', $task));

        $response->assertOk();
        $response->assertSee('Desain ERD Database');
        $response->assertSee('Gunakan skema 7 tabel');
        $response->assertSee('Pak Coach');
    }

    public function test_mahasiswa_cannot_view_detail_of_another_students_task(): void
    {
        $coach = User::factory()->coach()->create();
        $studentA = User::factory()->mahasiswa()->create();
        $studentB = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'Proyek Alpha',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach([$studentA->id, $studentB->id]);

        $taskB = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $studentB->id,
            'title' => 'Tugas Rahasia B',
            'status' => Task::STATUS_TODO,
        ]);

        $this->actingAs($studentA)
            ->get(route('mahasiswa.tasks.show', $taskB))
            ->assertForbidden();
    }

    public function test_mahasiswa_can_update_task_status_to_in_progress_and_progress_percentage(): void
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'Proyek Alpha',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Implementasi Modul Auth',
            'status' => Task::STATUS_TODO,
            'progress' => 0,
        ]);

        $response = $this->actingAs($student)->patch(route('mahasiswa.tasks.progress', $task), [
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => 50,
            'progress_note' => 'Halaman login dan logout selesai.',
        ]);

        $response->assertRedirect(route('mahasiswa.tasks.show', $task));
        $response->assertSessionHas('status', 'Progres tugas berhasil diperbarui.');

        $task->refresh();
        $this->assertSame(Task::STATUS_IN_PROGRESS, $task->status);
        $this->assertSame(50, $task->progress);
        $this->assertSame('Halaman login dan logout selesai.', $task->progress_note);
    }

    public function test_mahasiswa_cannot_set_task_status_to_done(): void
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'Proyek Alpha',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Implementasi Modul Auth',
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => 50,
        ]);

        $response = $this->actingAs($student)->patch(route('mahasiswa.tasks.progress', $task), [
            'status' => Task::STATUS_DONE,
            'progress' => 100,
            'progress_note' => 'Mencoba menandai selesai sendiri',
        ]);

        $response->assertSessionHasErrors(['status']);

        $task->refresh();
        $this->assertSame(Task::STATUS_IN_PROGRESS, $task->status);
        $this->assertSame(50, $task->progress);
    }

    public function test_mahasiswa_cannot_set_progress_outside_0_to_100(): void
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'Proyek Alpha',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Tugas Cek Range',
            'status' => Task::STATUS_TODO,
            'progress' => 0,
        ]);

        // Progres > 100
        $this->actingAs($student)->patch(route('mahasiswa.tasks.progress', $task), [
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => 150,
        ])->assertSessionHasErrors(['progress']);

        // Progres < 0
        $this->actingAs($student)->patch(route('mahasiswa.tasks.progress', $task), [
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => -5,
        ])->assertSessionHasErrors(['progress']);

        $task->refresh();
        $this->assertSame(0, $task->progress);
    }

    public function test_mahasiswa_cannot_update_progress_of_task_assigned_to_someone_else(): void
    {
        $coach = User::factory()->coach()->create();
        $studentA = User::factory()->mahasiswa()->create();
        $studentB = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'Proyek Alpha',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach([$studentA->id, $studentB->id]);

        $taskB = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $studentB->id,
            'title' => 'Tugas Mahasiswa B',
            'status' => Task::STATUS_TODO,
            'progress' => 0,
        ]);

        $this->actingAs($studentA)
            ->patch(route('mahasiswa.tasks.progress', $taskB), [
                'status' => Task::STATUS_IN_PROGRESS,
                'progress' => 20,
            ])
            ->assertForbidden();

        $taskB->refresh();
        $this->assertSame(Task::STATUS_TODO, $taskB->status);
        $this->assertSame(0, $taskB->progress);
    }

    public function test_mahasiswa_cannot_update_progress_of_task_that_is_already_done(): void
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'Proyek Alpha',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $doneTask = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Tugas Selesai',
            'status' => Task::STATUS_DONE,
            'progress' => 100,
        ]);

        $this->actingAs($student)
            ->patch(route('mahasiswa.tasks.progress', $doneTask), [
                'status' => Task::STATUS_IN_PROGRESS,
                'progress' => 90,
            ])
            ->assertForbidden();

        $doneTask->refresh();
        $this->assertSame(Task::STATUS_DONE, $doneTask->status);
        $this->assertSame(100, $doneTask->progress);
    }

    public function test_coach_and_admin_cannot_access_mahasiswa_task_endpoints(): void
    {
        $coach = User::factory()->coach()->create();
        $admin = User::factory()->admin()->create();
        $student = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'Proyek Alpha',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Tugas Mahasiswa',
            'status' => Task::STATUS_TODO,
        ]);

        // Coach cannot access Mahasiswa endpoints
        $this->actingAs($coach)->get(route('mahasiswa.tasks.index'))->assertForbidden();
        $this->actingAs($coach)->get(route('mahasiswa.tasks.show', $task))->assertForbidden();
        $this->actingAs($coach)->patch(route('mahasiswa.tasks.progress', $task), [
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => 10,
        ])->assertForbidden();

        // Admin cannot access Mahasiswa endpoints
        $this->actingAs($admin)->get(route('mahasiswa.tasks.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('mahasiswa.tasks.show', $task))->assertForbidden();
        $this->actingAs($admin)->patch(route('mahasiswa.tasks.progress', $task), [
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => 10,
        ])->assertForbidden();
    }
}
