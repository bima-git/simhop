<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Revision;
use App\Models\Task;
use App\Models\User;
use App\Models\VideoSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private function createSetup(): array
    {
        $coach = User::factory()->coach()->create();
        $student = User::factory()->mahasiswa()->create();

        $project = Project::create([
            'name' => 'Proyek Video',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);
        $project->members()->attach($student->id);

        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'assigned_to' => $student->id,
            'title' => 'Tugas Fitur Autentikasi',
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => 50,
        ]);

        return [$coach, $student, $project, $task];
    }

    public function test_mahasiswa_can_submit_valid_youtube_video(): void
    {
        [$coach, $student, $project, $task] = $this->createSetup();

        $response = $this->actingAs($student)->post(route('mahasiswa.videos.store', $task), [
            'title' => 'Demo Fitur Login',
            'description' => 'Video penjelasan login dan validasi',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response->assertRedirect(route('mahasiswa.tasks.show', $task));
        $response->assertSessionHas('status', 'Video dokumentasi berhasil dikirim.');

        $video = VideoSubmission::first();
        $this->assertNotNull($video);
        $this->assertSame($task->id, $video->task_id);
        $this->assertSame($student->id, $video->user_id);
        $this->assertSame('Demo Fitur Login', $video->title);
        $this->assertSame('pending', $video->status);
        $this->assertSame('youtube', $video->platform->value);
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $video->embed_url);
    }

    public function test_mahasiswa_can_submit_valid_google_drive_video(): void
    {
        [$coach, $student, $project, $task] = $this->createSetup();

        $driveUrl = 'https://drive.google.com/file/d/1a2b3c4d5e6f7g8h9i0j_kLmNoP/view?usp=sharing';

        $response = $this->actingAs($student)->post(route('mahasiswa.videos.store', $task), [
            'title' => 'Demo Drive',
            'video_url' => $driveUrl,
        ]);

        $response->assertRedirect(route('mahasiswa.tasks.show', $task));

        $video = VideoSubmission::first();
        $this->assertNotNull($video);
        $this->assertSame('google_drive', $video->platform->value);
        $this->assertSame('https://drive.google.com/file/d/1a2b3c4d5e6f7g8h9i0j_kLmNoP/preview', $video->embed_url);
    }

    public function test_invalid_video_url_is_rejected(): void
    {
        [$coach, $student, $project, $task] = $this->createSetup();

        // 1. Phishing / not youtube or drive
        $response = $this->actingAs($student)->post(route('mahasiswa.videos.store', $task), [
            'title' => 'Demo Fake',
            'video_url' => 'https://vimeo.com/123456789',
        ]);
        $response->assertSessionHasErrors(['video_url']);

        // 2. Malformed
        $response = $this->actingAs($student)->post(route('mahasiswa.videos.store', $task), [
            'title' => 'Demo Malformed',
            'video_url' => 'bukan-url-sama-sekali',
        ]);
        $response->assertSessionHasErrors(['video_url']);

        $this->assertDatabaseEmpty('video_submissions');
    }

    public function test_missing_title_is_rejected(): void
    {
        [$coach, $student, $project, $task] = $this->createSetup();

        $response = $this->actingAs($student)->post(route('mahasiswa.videos.store', $task), [
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response->assertSessionHasErrors(['title']);
        $this->assertDatabaseEmpty('video_submissions');
    }

    public function test_mahasiswa_cannot_submit_video_for_another_students_task(): void
    {
        [$coach, $student, $project, $task] = $this->createSetup();
        $anotherStudent = User::factory()->mahasiswa()->create();

        $this->actingAs($anotherStudent)
            ->post(route('mahasiswa.videos.store', $task), [
                'title' => 'Mencoba Menyusup',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertForbidden();

        $this->assertDatabaseEmpty('video_submissions');
    }

    public function test_mahasiswa_cannot_submit_video_if_task_is_already_done(): void
    {
        [$coach, $student, $project, $task] = $this->createSetup();
        $task->update(['status' => Task::STATUS_DONE]);

        $this->actingAs($student)
            ->post(route('mahasiswa.videos.store', $task), [
                'title' => 'Demo Fitur Selesai',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertForbidden();

        $this->assertDatabaseEmpty('video_submissions');
    }

    public function test_mahasiswa_cannot_store_duplicate_video_if_one_already_exists(): void
    {
        [$coach, $student, $project, $task] = $this->createSetup();

        // Submission pertama
        $task->videoSubmissions()->create([
            'user_id' => $student->id,
            'title' => 'Video Pertama',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'status' => 'pending',
        ]);

        // Coba store video kedua
        $this->actingAs($student)
            ->post(route('mahasiswa.videos.store', $task), [
                'title' => 'Video Kedua Duplikat',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertForbidden();

        $this->assertCount(1, VideoSubmission::all());
    }

    public function test_mahasiswa_can_resubmit_video_when_status_is_revision(): void
    {
        [$coach, $student, $project, $task] = $this->createSetup();

        $video = $task->videoSubmissions()->create([
            'user_id' => $student->id,
            'title' => 'Video Awal',
            'description' => 'Deskripsi lama',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'status' => 'revision',
            'reviewed_by' => $coach->id,
            'reviewed_at' => now(),
        ]);

        // Catatan revisi sebelumnya
        $revision = Revision::create([
            'video_submission_id' => $video->id,
            'coach_id' => $coach->id,
            'note' => 'Suara kurang jelas, tolong jelaskan bagian validasi form.',
        ]);

        $newYoutubeUrl = 'https://youtu.be/9bZkp7q19f0';

        $response = $this->actingAs($student)->put(route('mahasiswa.videos.update', $video), [
            'title' => 'Video Perbaikan Fitur Login',
            'description' => 'Sudah diperbaiki dengan narasi yang lebih jelas',
            'video_url' => $newYoutubeUrl,
        ]);

        $response->assertRedirect(route('mahasiswa.tasks.show', $task));
        $response->assertSessionHas('status', 'Video dokumentasi berhasil dikirim ulang.');

        // Hanya ada 1 baris di tabel video_submissions (menimpa baris yang sama, D-07)
        $this->assertSame(1, VideoSubmission::count());

        $video->refresh();
        $this->assertSame('Video Perbaikan Fitur Login', $video->title);
        $this->assertSame('Sudah diperbaiki dengan narasi yang lebih jelas', $video->description);
        $this->assertSame($newYoutubeUrl, $video->video_url);
        $this->assertSame('pending', $video->status);
        $this->assertNull($video->reviewed_by);
        $this->assertNull($video->reviewed_at);
        $this->assertSame('https://www.youtube-nocookie.com/embed/9bZkp7q19f0', $video->embed_url);

        // Riwayat revisi tetap utuh
        $this->assertCount(1, $video->revisions);
        $this->assertSame($revision->id, $video->revisions->first()->id);
    }

    public function test_mahasiswa_cannot_resubmit_when_status_is_pending_or_approved(): void
    {
        [$coach, $student, $project, $task] = $this->createSetup();

        // 1. Status 'pending' -> dilarang resubmit
        $videoPending = $task->videoSubmissions()->create([
            'user_id' => $student->id,
            'title' => 'Video Pending',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'status' => 'pending',
        ]);

        $this->actingAs($student)
            ->put(route('mahasiswa.videos.update', $videoPending), [
                'title' => 'Coba Ubah Pending',
                'video_url' => 'https://youtu.be/9bZkp7q19f0',
            ])
            ->assertForbidden();

        // 2. Status 'approved' -> dilarang resubmit
        $videoPending->update(['status' => 'approved']);

        $this->actingAs($student)
            ->put(route('mahasiswa.videos.update', $videoPending), [
                'title' => 'Coba Ubah Approved',
                'video_url' => 'https://youtu.be/9bZkp7q19f0',
            ])
            ->assertForbidden();
    }

    public function test_another_mahasiswa_cannot_resubmit_someone_elses_video(): void
    {
        [$coach, $student, $project, $task] = $this->createSetup();
        $anotherStudent = User::factory()->mahasiswa()->create();

        $video = $task->videoSubmissions()->create([
            'user_id' => $student->id,
            'title' => 'Video Student A',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'status' => 'revision',
        ]);

        $this->actingAs($anotherStudent)
            ->put(route('mahasiswa.videos.update', $video), [
                'title' => 'Coba Bajak Video',
                'video_url' => 'https://youtu.be/9bZkp7q19f0',
            ])
            ->assertForbidden();
    }

    public function test_coach_and_admin_cannot_submit_video_via_mahasiswa_routes(): void
    {
        [$coach, $student, $project, $task] = $this->createSetup();
        $admin = User::factory()->admin()->create();

        $this->actingAs($coach)
            ->post(route('mahasiswa.videos.store', $task), [
                'title' => 'Coach Coba Kirim',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('mahasiswa.videos.store', $task), [
                'title' => 'Admin Coba Kirim',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertForbidden();
    }
}
