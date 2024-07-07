<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Issue;
use Livewire\Livewire;
use App\Models\IssueTask;
use App\Livewire\Issues\IssuesView;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IssuesViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_be_logged_in_to_see_issue_view_page()
    {
        $issue = Issue::factory()->create();

        $this->get(route('issues.view', $issue))
            ->assertRedirect(route('login'));
    }

    public function test_logged_in_user_can_see_issue_view_page()
    {
        $user = User::factory()->create();

        $issue = Issue::factory()->create([
            'user_id' => $user->id
        ]);

        IssueTask::factory(5)->create([
            'issue_id' => $issue->id
        ]);

        // Ensure table is shown and issue is present
        $this->actingAs($user)
            ->get(route('issues.view', $issue))
            ->assertStatus(200)
            ->assertSeeLivewire('issues.issues-form')
            ->assertSeeLivewire('issues.issues-tasks-form')
            ->assertSeeLivewire('issues.issues-tasks-table')
            ->assertSeeLivewire('issues.issues-uploads-form')
            ->assertSeeLivewire('issues.issues-uploads-table')
            ->assertSee($issue->name);

        // Test delete
        Livewire::actingAs($user)
            ->test(IssuesView::class, ['issue' => $issue])
            ->call('getIssue')
            ->call('deleteIssue')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('issues', [
            'id' => $issue->id
        ]);
    }
}
