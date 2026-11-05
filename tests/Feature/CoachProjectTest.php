<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoachProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_coach_can_view_project_list_and_create_page(): void
    {
        $coach = User::factory()->coach()->create();

        $this->actingAs($coach)
            ->get(route('coach.projects.index'))
            ->assertOk();

        $this->actingAs($coach)
            ->get(route('coach.projects.create'))
            ->assertOk();

        $project = Project::create([
            'name' => 'Proyek Detail Test',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);

        $this->actingAs($coach)
            ->get(route('coach.projects.show', $project))
            ->assertOk()
            ->assertSee('Proyek Detail Test');
    }

    public function test_coach_can_create_project_with_valid_data(): void
    {
        $coach = User::factory()->coach()->create();

        $response = $this->actingAs($coach)->post(route('coach.projects.store'), [
            'name' => 'Proyek Alpha',
            'description' => 'Deskripsi proyek alpha',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]);

        $project = Project::first();

        $this->assertNotNull($project);
        $this->assertSame('Proyek Alpha', $project->name);
        $this->assertSame($coach->id, $project->created_by);
        $response->assertRedirect(route('coach.projects.show', $project));
        $response->assertSessionHas('status', 'Proyek berhasil dibuat.');
    }

    public function test_project_creation_fails_when_end_date_is_before_start_date(): void
    {
        $coach = User::factory()->coach()->create();

        $response = $this->actingAs($coach)->post(route('coach.projects.store'), [
            'name' => 'Proyek Gagal',
            'start_date' => '2026-11-30',
            'end_date' => '2026-11-01',
        ]);

        $response->assertSessionHasErrors(['end_date']);
        $this->assertDatabaseMissing('projects', ['name' => 'Proyek Gagal']);
    }

    public function test_coach_can_add_active_mahasiswa_as_member(): void
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();
        $project = Project::create([
            'name' => 'Proyek Beta',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($coach)->post(route('coach.projects.members.store', $project), [
            'user_id' => $student->id,
        ]);

        $response->assertSessionHas('status', 'Mahasiswa ditambahkan ke proyek.');
        $this->assertTrue($project->fresh()->members->contains($student));
    }

    public function test_coach_cannot_add_inactive_mahasiswa_or_non_mahasiswa_as_member(): void
    {
        $coach = User::factory()->coach()->create();
        $inactiveStudent = User::factory()->inactive()->create();
        $anotherCoach = User::factory()->coach()->create();
        $project = Project::create([
            'name' => 'Proyek Gamma',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);

        $this->actingAs($coach)
            ->post(route('coach.projects.members.store', $project), [
                'user_id' => $inactiveStudent->id,
            ])
            ->assertSessionHasErrors(['user_id']);

        $this->actingAs($coach)
            ->post(route('coach.projects.members.store', $project), [
                'user_id' => $anotherCoach->id,
            ])
            ->assertSessionHasErrors(['user_id']);

        $this->assertCount(0, $project->fresh()->members);
    }

    public function test_coach_can_remove_member_from_project(): void
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();
        $project = Project::create([
            'name' => 'Proyek Delta',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $response = $this->actingAs($coach)
            ->delete(route('coach.projects.members.destroy', [$project, $student]));

        $response->assertSessionHas('status', 'Mahasiswa dikeluarkan dari proyek.');
        $this->assertFalse($project->fresh()->members->contains($student));
    }

    public function test_coach_cannot_view_or_modify_other_coach_project(): void
    {
        $coachA = User::factory()->coach()->create();
        $coachB = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        $projectA = Project::create([
            'name' => 'Proyek Coach A',
            'created_by' => $coachA->id,
            'status' => 'active',
        ]);
        $projectA->members()->attach($student->id);

        // Coach B tries to view Coach A's project
        $this->actingAs($coachB)
            ->get(route('coach.projects.show', $projectA))
            ->assertForbidden();

        // Coach B tries to add member to Coach A's project
        $anotherStudent = User::factory()->mahasiswa()->create();
        $this->actingAs($coachB)
            ->post(route('coach.projects.members.store', $projectA), [
                'user_id' => $anotherStudent->id,
            ])
            ->assertForbidden();

        // Coach B tries to remove member from Coach A's project
        $this->actingAs($coachB)
            ->delete(route('coach.projects.members.destroy', [$projectA, $student]))
            ->assertForbidden();
    }

    public function test_mahasiswa_cannot_access_coach_project_routes(): void
    {
        $coach = User::factory()->coach()->create();
        $mahasiswa = User::factory()->mahasiswa()->create();
        $project = Project::create([
            'name' => 'Proyek Milik Coach',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);

        $this->actingAs($mahasiswa)->get(route('coach.projects.index'))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('coach.projects.create'))->assertForbidden();
        $this->actingAs($mahasiswa)->post(route('coach.projects.store'), ['name' => 'Test'])->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('coach.projects.show', $project))->assertForbidden();
    }
}
