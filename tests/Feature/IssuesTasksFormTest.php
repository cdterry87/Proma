<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Issue;
use Livewire\Livewire;
use App\Models\IssueTask;
use App\Livewire\Issues\IssuesTasksForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IssuesTasksFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_issue_task()
    {
        $user = User::factory()->create();

        $issue = Issue::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(IssuesTasksForm::class)
            ->call('getIssue', $issue->id)
            ->set('title', 'Issue Title')
            ->set('description', 'Issue Description')
            ->call('saveTask')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('issues_tasks', [
            'issue_id' => $issue->id,
            'title' => 'Issue Title',
            'description' => 'Issue Description',
        ]);
    }

    public function test_user_can_edit_and_complete_and_delete_issue_task()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $issue = Issue::factory()->create([
            'user_id' => $user->id,
        ]);

        $task = IssueTask::factory()->create([
            'issue_id' => $issue->id,
        ]);

        // Test edit and complete
        Livewire::test(IssuesTasksForm::class)
            ->call('editTask', $task->id)
            ->assertSet('task_id', $task->id)
            ->assertSet('title', $task->title)
            ->assertSet('description', $task->description)
            ->call('toggleCompleteTask');

        $this->assertDatabaseHas('issues_tasks', [
            'id' => $task->id,
            'completed_date' => now()->toDateString(),
        ]);

        Livewire::test(IssuesTasksForm::class)
            ->call('editTask', $task->id)
            ->assertSet('task_id', $task->id)
            ->call('toggleCompleteTask');

        $this->assertDatabaseHas('issues_tasks', [
            'id' => $task->id,
            'completed_date' => null,
        ]);

        // Test delete
        Livewire::test(IssuesTasksForm::class)
            ->call('editTask', $task->id)
            ->assertSet('task_id', $task->id)
            ->call('deleteTask', $task->id, $issue->id);

        $this->assertDatabaseMissing('issues_tasks', [
            'id' => $task->id,
        ]);
    }
}
