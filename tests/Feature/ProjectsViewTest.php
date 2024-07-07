<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Project;
use App\Livewire\Projects\ProjectsView;
use App\Models\ProjectTask;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectsViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_be_logged_in_to_see_project_view_page()
    {
        $project = Project::factory()->create();

        $this->get(route('projects.view', $project))
            ->assertRedirect(route('login'));
    }

    public function test_logged_in_user_can_see_project_view_page()
    {
        $user = User::factory()->create();

        $project = Project::factory()->create([
            'user_id' => $user->id
        ]);

        ProjectTask::factory(5)->create([
            'project_id' => $project->id
        ]);

        // Ensure table is shown and project is present
        $this->actingAs($user)
            ->get(route('projects.view', $project))
            ->assertStatus(200)
            ->assertSeeLivewire('projects.projects-form')
            ->assertSeeLivewire('projects.projects-tasks-form')
            ->assertSeeLivewire('projects.projects-tasks-table')
            ->assertSeeLivewire('projects.projects-uploads-form')
            ->assertSeeLivewire('projects.projects-uploads-table')
            ->assertSee($project->name);

        // Test delete
        Livewire::actingAs($user)
            ->test(ProjectsView::class, ['project' => $project])
            ->call('getProject')
            ->call('deleteProject')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id
        ]);
    }
}
