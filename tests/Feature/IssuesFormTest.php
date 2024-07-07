<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Issue;
use App\Models\Client;
use Livewire\Livewire;
use App\Models\Project;
use App\Livewire\Issues\IssuesForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IssuesFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_issue()
    {
        $user = User::factory()->create();

        $client = Client::factory()->create([
            'user_id' => $user->id,
            'active' => true,
        ]);

        $project = Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
        ]);

        $this->actingAs($user);

        Livewire::test(IssuesForm::class)
            ->set('name', 'Issue Name')
            ->set('description', 'Issue Description')
            ->set('priority', 3)
            ->set('client_id', $client->id)
            ->set('project_id', $project->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('issues', [
            'user_id' => $user->id,
            'client_id' => $client->id,
            'project_id' => $project->id,
            'name' => 'Issue Name',
            'description' => 'Issue Description',
            'priority' => 3,
        ]);
    }

    public function test_user_can_edit_and_resolve_issue()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $issue = Issue::factory()->create([
            'user_id' => $user->id,
            'resolved_date' => null,
        ]);

        Livewire::test(IssuesForm::class)
            ->call('edit', $issue->id)
            ->assertSet('model_id', $issue->id)
            ->assertSet('user_id', $issue->user_id)
            ->assertSet('client_id', $issue->client_id)
            ->assertSet('name', $issue->name)
            ->assertSet('description', $issue->description)
            ->assertSet('priority', $issue->priority)
            ->assertSet('resolved_date', null)
            ->call('toggleResolveIssue')
            ->assertHasNoErrors()
            ->call('closeModal');

        $this->assertDatabaseHas('issues', [
            'id' => $issue->id,
            'resolved_date' => now()->format('Y-m-d'),
        ]);

        Livewire::test(IssuesForm::class)
            ->call('edit', $issue->id)
            ->call('toggleResolveIssue')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('issues', [
            'id' => $issue->id,
            'resolved_date' => null,
        ]);
    }
}
