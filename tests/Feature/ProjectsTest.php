<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_be_logged_in_to_see_projects_page()
    {
        $this->get(route('projects'))
            ->assertRedirect(route('login'));
    }

    public function test_logged_in_user_can_see_projects_page()
    {
        $user = User::factory()->create();

        Project::factory(20)->create([
            'user_id' => $user->id
        ]);

        // Get a random project
        $project = Project::where('user_id', $user->id)->first();

        // Ensure table is shown and project is present
        $this->actingAs($user)
            ->get(route('projects'))
            ->assertStatus(200)
            ->assertSeeLivewire('projects.projects-table')
            ->assertSeeLivewire('projects.projects-form')
            ->assertSee($project->name);
    }
}
