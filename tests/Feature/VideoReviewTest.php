<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Revision;
use App\Models\Task;
use App\Models\User;
use App\Models\VideoSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoReviewTest extends TestCase
{
    use RefreshDatabase;

    private function createSetup(): array
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'Proyek Review',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Tugas Modul Review',
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => 60,
        ]);

        $video = $task->videoSubmissions()->create([
            'user_id' => $student->id,
            'title' => 'Video Demo Fitur Modul',
            'description' => 'Demo fitur lengkap',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'status' => 'pending',
        ]);

        return [$coach, $student, $project, $task, $video];
    }

    public function test_coach_can_view_video_review_index_and_filter(): void
    {
        [$coach, $student, $project, $task, $video] = $this->createSetup();

        // 1. Default index (pending queue)
        $response = $this->actingAs($coach)->get(route('coach.videos.index'));
        $response->assertOk()
            ->assertSee('Video Demo Fitur Modul')
            ->assertSee($student->name);

        // 2. Filter tabs
        $this->actingAs($coach)->get(route('coach.videos.index', ['status' => 'pending']))->assertOk();
        $this->actingAs($coach)->get(route('coach.videos.index', ['status' => 'revision']))->assertOk();
        $this->actingAs($coach)->get(route('coach.videos.index', ['status' => 'approved']))->assertOk();
        $this->actingAs($coach)->get(route('coach.videos.index', ['status' => 'all']))->assertOk();
    }

    public function test_coach_can_view_video_review_detail_page(): void
    {
        [$coach, $student, $project, $task, $video] = $this->createSetup();

        $response = $this->actingAs($coach)->get(route('coach.videos.show', $video));
        $response->assertOk()
            ->assertSee('Video Demo Fitur Modul')
            ->assertSee($task->title)
            ->assertSee($student->name)
            ->assertSee('Setujui Video Ini')
            ->assertSee('Minta Revisi');
    }

    public function test_coach_can_approve_video_and_task_is_marked_as_done(): void
    {
        [$coach, $student, $project, $task, $video] = $this->createSetup();

        $response = $this->actingAs($coach)->post(route('coach.videos.approve', $video));

        $response->assertRedirect(route('coach.videos.show', $video));
        $response->assertSessionHas('status', 'Video dokumentasi berhasil disetujui dan tugas telah ditandai selesai.');

        // Video status becomes 'approved', reviewed_by and reviewed_at populated
        $video->refresh();
        $this->assertSame('approved', $video->status);
        $this->assertSame($coach->id, $video->reviewed_by);
        $this->assertNotNull($video->reviewed_at);

        // Task status becomes 'done' and progress is 100% (BR-25, BR-46, D-08)
        $task->refresh();
        $this->assertSame(Task::STATUS_DONE, $task->status);
        $this->assertSame(100, $task->progress);
    }

    public function test_coach_cannot_approve_already_approved_video(): void
    {
        [$coach, $student, $project, $task, $video] = $this->createSetup();
        $video->update(['status' => 'approved']);

        $this->actingAs($coach)
            ->post(route('coach.videos.approve', $video))
            ->assertStatus(422);
    }

    public function test_coach_cannot_request_revision_without_note(): void
    {
        [$coach, $student, $project, $task, $video] = $this->createSetup();

        // 1. Catatan kosong
        $response = $this->actingAs($coach)->post(route('coach.videos.revise', $video), [
            'note' => '',
        ]);
        $response->assertSessionHasErrors(['note']);

        // 2. Catatan hanya berisi spasi
        $response = $this->actingAs($coach)->post(route('coach.videos.revise', $video), [
            'note' => '     ',
        ]);
        $response->assertSessionHasErrors(['note']);

        // Tidak ada baris revisi dibuat, status tetap pending
        $this->assertDatabaseEmpty('revisions');
        $this->assertSame('pending', $video->fresh()->status);
    }

    public function test_coach_can_request_revision_with_valid_note(): void
    {
        [$coach, $student, $project, $task, $video] = $this->createSetup();

        $response = $this->actingAs($coach)->post(route('coach.videos.revise', $video), [
            'note' => 'Video terlalu singkat. Tolong demokan juga skenario error validasinya.',
        ]);

        $response->assertRedirect(route('coach.videos.show', $video));
        $response->assertSessionHas('status', 'Catatan revisi berhasil dikirim ke mahasiswa.');

        // Video status becomes 'revision'
        $video->refresh();
        $this->assertSame('revision', $video->status);
        $this->assertSame($coach->id, $video->reviewed_by);
        $this->assertNotNull($video->reviewed_at);

        // Record in revisions table created
        $this->assertDatabaseHas('revisions', [
            'video_submission_id' => $video->id,
            'coach_id' => $coach->id,
            'note' => 'Video terlalu singkat. Tolong demokan juga skenario error validasinya.',
        ]);

        // Task status remains in_progress (BR-48)
        $task->refresh();
        $this->assertSame(Task::STATUS_IN_PROGRESS, $task->status);
        $this->assertSame(60, $task->progress);
    }

    public function test_revision_history_is_chronological_with_coach_and_timestamp(): void
    {
        [$coach, $student, $project, $task, $video] = $this->createSetup();

        // Revisi 1
        $rev1 = Revision::create([
            'video_submission_id' => $video->id,
            'coach_id' => $coach->id,
            'note' => 'Revisi Pertama: Jelaskan alur login.',
            'created_at' => now()->subDay(),
        ]);

        // Revisi 2
        $rev2 = Revision::create([
            'video_submission_id' => $video->id,
            'coach_id' => $coach->id,
            'note' => 'Revisi Kedua: Tambahkan tes integrasi.',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($coach)->get(route('coach.videos.show', $video));
        $response->assertOk()
            ->assertSee('Revisi Pertama: Jelaskan alur login.')
            ->assertSee('Revisi Kedua: Tambahkan tes integrasi.');

        $revisions = $video->fresh()->revisions;
        $this->assertSame($rev1->id, $revisions->first()->id);
        $this->assertSame($rev2->id, $revisions->last()->id);
    }

    public function test_another_coach_cannot_view_approve_or_revise_video(): void
    {
        [$coachA, $student, $project, $task, $video] = $this->createSetup();
        $coachB = User::factory()->coach()->create();

        // Coach B tries to view
        $this->actingAs($coachB)
            ->get(route('coach.videos.show', $video))
            ->assertForbidden();

        // Coach B tries to approve
        $this->actingAs($coachB)
            ->post(route('coach.videos.approve', $video))
            ->assertForbidden();

        // Coach B tries to revise
        $this->actingAs($coachB)
            ->post(route('coach.videos.revise', $video), [
                'note' => 'Revisi dari coach tak berwenang',
            ])
            ->assertForbidden();
    }

    public function test_mahasiswa_and_admin_cannot_access_coach_video_review_routes(): void
    {
        [$coach, $student, $project, $task, $video] = $this->createSetup();
        $admin = User::factory()->admin()->create();

        // Mahasiswa 403
        $this->actingAs($student)->get(route('coach.videos.index'))->assertForbidden();
        $this->actingAs($student)->get(route('coach.videos.show', $video))->assertForbidden();
        $this->actingAs($student)->post(route('coach.videos.approve', $video))->assertForbidden();
        $this->actingAs($student)->post(route('coach.videos.revise', $video), ['note' => 'test'])->assertForbidden();

        // Admin 403
        $this->actingAs($admin)->get(route('coach.videos.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('coach.videos.show', $video))->assertForbidden();
        $this->actingAs($admin)->post(route('coach.videos.approve', $video))->assertForbidden();
        $this->actingAs($admin)->post(route('coach.videos.revise', $video), ['note' => 'test'])->assertForbidden();
    }

    public function test_approve_transaction_rolls_back_if_task_fails(): void
    {
        [$coach, $student, $project, $task, $video] = $this->createSetup();

        // Simulasikan kegagalan saat Task::markAsDone()
        Task::saving(function ($t) {
            if ($t->status === Task::STATUS_DONE) {
                throw new \RuntimeException('Simulasi gagal simpan task selesai');
            }
        });

        try {
            $this->actingAs($coach)->post(route('coach.videos.approve', $video));
        } catch (\RuntimeException $e) {
            // Error tertangkap
        }

        // Kedua tabel harus kembali ke state awal karena transaksi DB di-rollback
        $video->refresh();
        $this->assertSame('pending', $video->status);
        $this->assertNull($video->reviewed_by);

        $task->refresh();
        $this->assertSame(Task::STATUS_IN_PROGRESS, $task->status);
        $this->assertSame(60, $task->progress);
    }

    public function test_revise_transaction_rolls_back_if_video_update_fails(): void
    {
        [$coach, $student, $project, $task, $video] = $this->createSetup();

        // Simulasikan kegagalan saat VideoSubmission diupdate
        VideoSubmission::saving(function ($v) {
            if ($v->status === 'revision') {
                throw new \RuntimeException('Simulasi gagal simpan video revisi');
            }
        });

        try {
            $this->actingAs($coach)->post(route('coach.videos.revise', $video), [
                'note' => 'Catatan revisi yang seharusnya rollback',
            ]);
        } catch (\RuntimeException $e) {
            // Error tertangkap
        }

        // Catatan revisi tidak boleh tersimpan karena di-rollback
        $this->assertDatabaseEmpty('revisions');

        $video->refresh();
        $this->assertSame('pending', $video->status);
        $this->assertNull($video->reviewed_by);
    }
}
