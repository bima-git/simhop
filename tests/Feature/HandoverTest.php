<?php

namespace Tests\Feature;

use App\Models\HandoverDocument;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HandoverTest extends TestCase
{
    use RefreshDatabase;

    private function createProjectWithCoach(): array
    {
        $coach = User::factory()->coach()->create();
        $project = Project::create([
            'name' => 'Proyek Handover',
            'created_by' => $coach->id,
            'status' => 'active',
        ]);

        return [$coach, $project];
    }

    // ===== COACH CRUD =====

    public function test_coach_can_view_handover_index_and_create_page(): void
    {
        [$coach, $project] = $this->createProjectWithCoach();

        $this->actingAs($coach)
            ->get(route('coach.handover.index', $project))
            ->assertOk();

        $this->actingAs($coach)
            ->get(route('coach.handover.create', $project))
            ->assertOk();
    }

    public function test_coach_can_create_text_only_handover_document(): void
    {
        [$coach, $project] = $this->createProjectWithCoach();

        $response = $this->actingAs($coach)->post(route('coach.handover.store', $project), [
            'title' => 'Panduan Deployment',
            'type' => 'guide',
            'content' => 'Jalankan docker compose up -d',
        ]);

        $response->assertRedirect(route('coach.handover.index', $project));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('handover_documents', [
            'project_id' => $project->id,
            'title' => 'Panduan Deployment',
            'type' => 'guide',
            'uploaded_by' => $coach->id,
            'file_path' => null,
        ]);
    }

    public function test_coach_can_create_handover_document_with_file(): void
    {
        Storage::fake('local');

        [$coach, $project] = $this->createProjectWithCoach();

        $file = UploadedFile::fake()->create('setup.pdf', 500, 'application/pdf');

        $response = $this->actingAs($coach)->post(route('coach.handover.store', $project), [
            'title' => 'Dokumen Setup',
            'type' => 'tech_stack',
            'content' => 'Lihat PDF terlampir',
            'file' => $file,
        ]);

        $response->assertRedirect(route('coach.handover.index', $project));

        $doc = HandoverDocument::first();
        $this->assertNotNull($doc->file_path);
        $this->assertSame('setup.pdf', $doc->file_name);
        Storage::disk('local')->assertExists($doc->file_path);
    }

    public function test_file_over_10mb_is_rejected(): void
    {
        Storage::fake('local');

        [$coach, $project] = $this->createProjectWithCoach();

        // 11000 KB = ~10.7 MB, more than 10240 KB limit
        $file = UploadedFile::fake()->create('huge.pdf', 11000, 'application/pdf');

        $response = $this->actingAs($coach)->post(route('coach.handover.store', $project), [
            'title' => 'Dokumen Besar',
            'type' => 'asset',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors(['file']);
        $this->assertDatabaseMissing('handover_documents', ['title' => 'Dokumen Besar']);
    }

    public function test_forbidden_file_type_is_rejected(): void
    {
        Storage::fake('local');

        [$coach, $project] = $this->createProjectWithCoach();

        $file = UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($coach)->post(route('coach.handover.store', $project), [
            'title' => 'Berkas Berbahaya',
            'type' => 'asset',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors(['file']);
        $this->assertDatabaseMissing('handover_documents', ['title' => 'Berkas Berbahaya']);
    }

    public function test_coach_can_edit_and_update_handover_document(): void
    {
        Storage::fake('local');

        [$coach, $project] = $this->createProjectWithCoach();

        $oldFile = UploadedFile::fake()->create('old.pdf', 200, 'application/pdf');
        $oldPath = $oldFile->store('handover/' . $project->id, 'local');

        $doc = $project->handoverDocuments()->create([
            'title' => 'Judul Lama',
            'type' => 'guide',
            'content' => 'Konten lama',
            'uploaded_by' => $coach->id,
            'file_path' => $oldPath,
            'file_name' => 'old.pdf',
            'file_size' => 200,
        ]);

        // View edit page
        $this->actingAs($coach)
            ->get(route('coach.handover.edit', $doc))
            ->assertOk();

        // Update with new file
        $newFile = UploadedFile::fake()->create('new.pdf', 300, 'application/pdf');

        $response = $this->actingAs($coach)->put(route('coach.handover.update', $doc), [
            'title' => 'Judul Baru',
            'type' => 'tech_stack',
            'content' => 'Konten baru',
            'file' => $newFile,
        ]);

        $response->assertRedirect(route('coach.handover.index', $project));

        $doc->refresh();
        $this->assertSame('Judul Baru', $doc->title);
        $this->assertSame('tech_stack', $doc->type);
        $this->assertSame('new.pdf', $doc->file_name);

        // Old file should be deleted
        Storage::disk('local')->assertMissing($oldPath);
        // New file should exist
        Storage::disk('local')->assertExists($doc->file_path);
    }

    public function test_coach_can_delete_handover_document_and_file(): void
    {
        Storage::fake('local');

        [$coach, $project] = $this->createProjectWithCoach();

        $file = UploadedFile::fake()->create('remove.pdf', 100, 'application/pdf');
        $filePath = $file->store('handover/' . $project->id, 'local');

        $doc = $project->handoverDocuments()->create([
            'title' => 'Akan Dihapus',
            'type' => 'asset',
            'uploaded_by' => $coach->id,
            'file_path' => $filePath,
            'file_name' => 'remove.pdf',
            'file_size' => 100,
        ]);

        $response = $this->actingAs($coach)->delete(route('coach.handover.destroy', $doc));

        $response->assertRedirect(route('coach.handover.index', $project));
        $this->assertDatabaseMissing('handover_documents', ['id' => $doc->id]);
        Storage::disk('local')->assertMissing($filePath);
    }

    // ===== AKSES KONTROL =====

    public function test_other_coach_cannot_access_handover(): void
    {
        [$coachA, $project] = $this->createProjectWithCoach();
        $coachB = User::factory()->coach()->create();

        $doc = $project->handoverDocuments()->create([
            'title' => 'Rahasia',
            'type' => 'guide',
            'uploaded_by' => $coachA->id,
        ]);

        $this->actingAs($coachB)
            ->get(route('coach.handover.index', $project))
            ->assertForbidden();

        $this->actingAs($coachB)
            ->get(route('coach.handover.create', $project))
            ->assertForbidden();

        $this->actingAs($coachB)
            ->post(route('coach.handover.store', $project), [
                'title' => 'Coba',
                'type' => 'guide',
            ])
            ->assertForbidden();

        $this->actingAs($coachB)
            ->get(route('coach.handover.edit', $doc))
            ->assertForbidden();

        $this->actingAs($coachB)
            ->put(route('coach.handover.update', $doc), [
                'title' => 'Hack',
                'type' => 'guide',
            ])
            ->assertForbidden();

        $this->actingAs($coachB)
            ->delete(route('coach.handover.destroy', $doc))
            ->assertForbidden();
    }

    // ===== UNDUH =====

    public function test_member_mahasiswa_can_download_handover_file(): void
    {
        Storage::fake('local');

        [$coach, $project] = $this->createProjectWithCoach();
        $student = User::factory()->mahasiswa()->create();
        $project->members()->attach($student->id);

        $file = UploadedFile::fake()->create('guide.pdf', 100, 'application/pdf');
        $filePath = $file->store('handover/' . $project->id, 'local');

        $doc = $project->handoverDocuments()->create([
            'title' => 'Panduan',
            'type' => 'guide',
            'uploaded_by' => $coach->id,
            'file_path' => $filePath,
            'file_name' => 'guide.pdf',
            'file_size' => 100,
        ]);

        $this->actingAs($student)
            ->get(route('handover.download', $doc))
            ->assertOk();
    }

    public function test_non_member_mahasiswa_cannot_download_handover_file(): void
    {
        Storage::fake('local');

        [$coach, $project] = $this->createProjectWithCoach();
        $outsider = User::factory()->mahasiswa()->create();
        // Outsider is NOT a member of the project

        $file = UploadedFile::fake()->create('secret.pdf', 100, 'application/pdf');
        $filePath = $file->store('handover/' . $project->id, 'local');

        $doc = $project->handoverDocuments()->create([
            'title' => 'Rahasia',
            'type' => 'guide',
            'uploaded_by' => $coach->id,
            'file_path' => $filePath,
            'file_name' => 'secret.pdf',
            'file_size' => 100,
        ]);

        $this->actingAs($outsider)
            ->get(route('handover.download', $doc))
            ->assertForbidden();
    }

    public function test_coach_owner_can_download_handover_file(): void
    {
        Storage::fake('local');

        [$coach, $project] = $this->createProjectWithCoach();

        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');
        $filePath = $file->store('handover/' . $project->id, 'local');

        $doc = $project->handoverDocuments()->create([
            'title' => 'Report',
            'type' => 'asset',
            'uploaded_by' => $coach->id,
            'file_path' => $filePath,
            'file_name' => 'report.pdf',
            'file_size' => 100,
        ]);

        $this->actingAs($coach)
            ->get(route('handover.download', $doc))
            ->assertOk();
    }

    // ===== MAHASISWA VIEW =====

    public function test_member_mahasiswa_can_view_project_handover(): void
    {
        [$coach, $project] = $this->createProjectWithCoach();
        $student = User::factory()->mahasiswa()->create();
        $project->members()->attach($student->id);

        $project->handoverDocuments()->create([
            'title' => 'Panduan Dev',
            'type' => 'guide',
            'content' => 'Step 1...',
            'uploaded_by' => $coach->id,
        ]);

        $this->actingAs($student)
            ->get(route('mahasiswa.projects.show', $project))
            ->assertOk()
            ->assertSee('Panduan Dev')
            ->assertSee('Step 1...');
    }

    public function test_non_member_mahasiswa_cannot_view_project(): void
    {
        [$coach, $project] = $this->createProjectWithCoach();
        $outsider = User::factory()->mahasiswa()->create();

        $this->actingAs($outsider)
            ->get(route('mahasiswa.projects.show', $project))
            ->assertForbidden();
    }
}
