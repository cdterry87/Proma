<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Issue;
use Livewire\Livewire;
use App\Models\Project;
use App\Models\IssueTask;
use App\Models\ProjectTask;
use App\Livewire\Issues\IssuesTasksForm;
use App\Livewire\Projects\ProjectsTasksForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectsTasksFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_project_task()
    {
        $user = User::factory()->create();

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(ProjectsTasksForm::class)
            ->call('getProject', $project->id)
            ->set('title', 'Project Title')
            ->set('description', 'Project Description')
            ->set('start_date', now()->toDateString())
            ->set('due_date', now()->addDays(7)->toDateString())
            ->call('saveTask')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects_tasks', [
            'project_id' => $project->id,
            'title' => 'Project Title',
            'description' => 'Project Description',
            'start_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
        ]);
    }

    public function test_user_can_edit_and_complete_and_delete_project_task()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
        ]);

        // Test edit and complete
        Livewire::test(ProjectsTasksForm::class)
            ->call('editTask', $task->id)
            ->assertSet('task_id', $task->id)
            ->assertSet('title', $task->title)
            ->assertSet('description', $task->description)
            ->call('toggleCompleteTask');

        $this->assertDatabaseHas('projects_tasks', [
            'id' => $task->id,
            'completed_date' => now()->toDateString(),
        ]);

        Livewire::test(ProjectsTasksForm::class)
            ->call('editTask', $task->id)
            ->assertSet('task_id', $task->id)
            ->call('toggleCompleteTask');

        $this->assertDatabaseHas('projects_tasks', [
            'id' => $task->id,
            'completed_date' => null,
        ]);

        // Test delete
        Livewire::test(ProjectsTasksForm::class)
            ->call('editTask', $task->id)
            ->assertSet('task_id', $task->id)
            ->call('deleteTask', $task->id, $project->id);

        $this->assertDatabaseMissing('projects_tasks', [
            'id' => $task->id,
        ]);
    }
}
