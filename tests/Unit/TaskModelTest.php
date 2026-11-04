<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_mark_as_done_sets_status_to_done_and_progress_to_100(): void
    {
        $coach = User::factory()->coach()->create();
        $project = Project::create([
            'name' => 'Proyek Uji',
            'created_by' => $coach->id,
            'start_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $coach->id,
            'title' => 'Tugas Uji',
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => 45,
        ]);

        $this->assertSame(Task::STATUS_IN_PROGRESS, $task->status);
        $this->assertSame(45, $task->progress);

        $task->markAsDone();

        $task->refresh();
        $this->assertSame(Task::STATUS_DONE, $task->status);
        $this->assertSame(100, $task->progress);
    }

    public function test_task_has_expected_status_constants(): void
    {
        $this->assertSame('todo', Task::STATUS_TODO);
        $this->assertSame('in_progress', Task::STATUS_IN_PROGRESS);
        $this->assertSame('done', Task::STATUS_DONE);
    }
}
