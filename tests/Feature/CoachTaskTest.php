<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoachTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_coach_can_create_task_with_member_assigned(): void
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();
        $project = Project::create([
            'name' => 'Proyek Web',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $response = $this->actingAs($coach)->post(route('coach.projects.tasks.store', $project), [
            'title' => 'Slicing UI Dashboard',
            'description' => 'Gunakan Tailwind CSS',
            'due_date' => '2026-12-01',
            'assigned_to' => $student->id,
        ]);

        $response->assertRedirect(route('coach.projects.show', $project));
        $response->assertSessionHas('status', 'Tugas berhasil ditambahkan.');

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Slicing UI Dashboard',
            'description' => 'Gunakan Tailwind CSS',
            'status' => Task::STATUS_TODO,
            'progress' => 0,
        ]);
    }

    public function test_coach_can_create_task_without_assignee(): void
    {
        $coach = User::factory()->coach()->create();
        $project = Project::create([
            'name' => 'Proyek Web',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($coach)->post(route('coach.projects.tasks.store', $project), [
            'title' => 'Setup Database',
            'description' => 'Konfigurasi skema awal',
            'due_date' => null,
            'assigned_to' => null,
        ]);

        $response->assertRedirect(route('coach.projects.show', $project));
        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => null,
            'title' => 'Setup Database',
            'status' => Task::STATUS_TODO,
            'progress' => 0,
        ]);
    }

    public function test_coach_cannot_assign_task_to_non_member(): void
    {
        $coach = User::factory()->coach()->create();
        $nonMemberStudent = User::factory()->mahasiswa()->create();
        $project = Project::create([
            'name' => 'Proyek Web',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($coach)->post(route('coach.projects.tasks.store', $project), [
            'title' => 'Tugas Khusus',
            'assigned_to' => $nonMemberStudent->id,
        ]);

        $response->assertSessionHasErrors(['assigned_to']);
        $this->assertDatabaseMissing('tasks', ['title' => 'Tugas Khusus']);
    }

    public function test_coach_cannot_assign_task_to_non_mahasiswa(): void
    {
        $coach = User::factory()->coach()->create();
        $anotherCoach = User::factory()->coach()->create();
        $project = Project::create([
            'name' => 'Proyek Web',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($anotherCoach->id);

        $response = $this->actingAs($coach)->post(route('coach.projects.tasks.store', $project), [
            'title' => 'Tugas Khusus',
            'assigned_to' => $anotherCoach->id,
        ]);

        $response->assertSessionHasErrors(['assigned_to']);
        $this->assertDatabaseMissing('tasks', ['title' => 'Tugas Khusus']);
    }

    public function test_coach_cannot_assign_task_to_inactive_mahasiswa(): void
    {
        $coach = User::factory()->coach()->create();
        $inactiveStudent = User::factory()->inactive()->create();
        $project = Project::create([
            'name' => 'Proyek Web',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($inactiveStudent->id);

        $response = $this->actingAs($coach)->post(route('coach.projects.tasks.store', $project), [
            'title' => 'Tugas Khusus',
            'assigned_to' => $inactiveStudent->id,
        ]);

        $response->assertSessionHasErrors(['assigned_to']);
        $this->assertDatabaseMissing('tasks', ['title' => 'Tugas Khusus']);
    }

    public function test_task_creation_fails_without_title(): void
    {
        $coach = User::factory()->coach()->create();
        $project = Project::create([
            'name' => 'Proyek Web',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($coach)->post(route('coach.projects.tasks.store', $project), [
            'title' => '',
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_coach_can_view_edit_task_page(): void
    {
        $coach = User::factory()->coach()->create();
        $project = Project::create([
            'name' => 'Proyek Web',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $task = $project->tasks()->create([
            'title' => 'Tugas Awal',
            'created_by' => $coach->id,
            'status' => Task::STATUS_TODO,
        ]);

        $response = $this->actingAs($coach)->get(route('coach.tasks.edit', $task));

        $response->assertOk();
        $response->assertSee('Tugas Awal');
    }

    public function test_coach_can_update_task_and_cannot_tamper_status_or_progress(): void
    {
        $coach = User::factory()->coach()->create();
        $studentA = User::factory()->mahasiswa()->create();
        $studentB = User::factory()->mahasiswa()->create();
        $project = Project::create([
            'name' => 'Proyek Web',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach([$studentA->id, $studentB->id]);

        $task = $project->tasks()->create([
            'title' => 'Judul Lama',
            'description' => 'Deskripsi Lama',
            'due_date' => '2026-11-10',
            'assigned_to' => $studentA->id,
            'created_by' => $coach->id,
            'status' => Task::STATUS_TODO,
            'progress' => 0,
        ]);

        $response = $this->actingAs($coach)->put(route('coach.tasks.update', $task), [
            'title' => 'Judul Baru',
            'description' => 'Deskripsi Baru',
            'due_date' => '2026-11-20',
            'assigned_to' => $studentB->id,
            // Mencoba manipulasi status & progress lewat form
            'status' => Task::STATUS_DONE,
            'progress' => 100,
        ]);

        $response->assertRedirect(route('coach.projects.show', $project));
        $response->assertSessionHas('status', 'Tugas berhasil diperbarui.');

        $task->refresh();
        $this->assertSame('Judul Baru', $task->title);
        $this->assertSame('Deskripsi Baru', $task->description);
        $this->assertSame('2026-11-20', $task->due_date->format('Y-m-d'));
        $this->assertSame($studentB->id, $task->assigned_to);
        // Status dan progres tetap tidak berubah oleh form coach
        $this->assertSame(Task::STATUS_TODO, $task->status);
        $this->assertSame(0, $task->progress);
    }

    public function test_coach_can_delete_task(): void
    {
        $coach = User::factory()->coach()->create();
        $project = Project::create([
            'name' => 'Proyek Web',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $task = $project->tasks()->create([
            'title' => 'Tugas Akan Dihapus',
            'created_by' => $coach->id,
            'status' => Task::STATUS_TODO,
        ]);

        $response = $this->actingAs($coach)->delete(route('coach.tasks.destroy', $task));

        $response->assertRedirect(route('coach.projects.show', $project));
        $response->assertSessionHas('status', 'Tugas berhasil dihapus.');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_coach_cannot_create_task_in_other_coach_project(): void
    {
        $coachA = User::factory()->coach()->create();
        $coachB = User::factory()->coach()->create();
        $projectA = Project::create([
            'name' => 'Proyek Coach A',
            'created_by' => $coachA->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($coachB)->post(route('coach.projects.tasks.store', $projectA), [
            'title' => 'Tugas Ilegal',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('tasks', ['title' => 'Tugas Ilegal']);
    }

    public function test_coach_cannot_view_edit_update_or_delete_other_coach_task(): void
    {
        $coachA = User::factory()->coach()->create();
        $coachB = User::factory()->coach()->create();
        $projectA = Project::create([
            'name' => 'Proyek Coach A',
            'created_by' => $coachA->id,
            'status' => 'active',
        ]);
        $task = $projectA->tasks()->create([
            'title' => 'Tugas Milik Coach A',
            'created_by' => $coachA->id,
            'status' => Task::STATUS_TODO,
        ]);

        // GET edit
        $this->actingAs($coachB)
            ->get(route('coach.tasks.edit', $task))
            ->assertForbidden();

        // PUT update
        $this->actingAs($coachB)
            ->put(route('coach.tasks.update', $task), [
                'title' => 'Perubahan Ilegal',
            ])
            ->assertForbidden();

        // DELETE destroy
        $this->actingAs($coachB)
            ->delete(route('coach.tasks.destroy', $task))
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Tugas Milik Coach A']);
    }

    public function test_mahasiswa_cannot_access_coach_task_endpoints(): void
    {
        $coach = User::factory()->coach()->create();
        $mahasiswa = User::factory()->mahasiswa()->create();
        $project = Project::create([
            'name' => 'Proyek Coach',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($mahasiswa->id);
        $task = $project->tasks()->create([
            'title' => 'Tugas Mahasiswa',
            'assigned_to' => $mahasiswa->id,
            'created_by' => $coach->id,
            'status' => Task::STATUS_TODO,
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('coach.projects.tasks.store', $project), ['title' => 'Tugas'])
            ->assertForbidden();

        $this->actingAs($mahasiswa)
            ->get(route('coach.tasks.edit', $task))
            ->assertForbidden();

        $this->actingAs($mahasiswa)
            ->put(route('coach.tasks.update', $task), ['title' => 'Update'])
            ->assertForbidden();

        $this->actingAs($mahasiswa)
            ->delete(route('coach.tasks.destroy', $task))
            ->assertForbidden();
    }
}
