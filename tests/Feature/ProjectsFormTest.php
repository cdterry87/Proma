<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Livewire\Livewire;
use App\Models\Project;
use App\Livewire\Projects\ProjectsForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectsFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_project()
    {
        $user = User::factory()->create();

        $client = Client::factory()->create([
            'user_id' => $user->id,
            'active' => true,
        ]);

        $this->actingAs($user);

        Livewire::test(ProjectsForm::class)
            ->set('name', 'Project Name')
            ->set('description', 'Project Description')
            ->set('client_id', $client->id)
            ->set('start_date', now()->format('Y-m-d'))
            ->set('due_date', now()->addDays(7)->format('Y-m-d'))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'user_id' => $user->id,
            'name' => 'Project Name',
            'description' => 'Project Description',
            'client_id' => $client->id,
            'start_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ]);
    }

    public function test_user_can_edit_and_complete_project()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $project = Project::factory()->create([
            'user_id' => $user->id,
            'completed_date' => null
        ]);

        Livewire::test(ProjectsForm::class)
            ->call('edit', $project->id)
            ->assertSet('model_id', $project->id)
            ->assertSet('user_id', $project->user_id)
            ->assertSet('name', $project->name)
            ->assertSet('description', $project->description)
            ->assertSet('client_id', $project->client_id)
            ->assertSet('start_date', $project->start_date->format('Y-m-d'))
            ->assertSet('due_date', $project->due_date->format('Y-m-d'))
            ->assertSet('completed_date', null)
            ->call('toggleCompleteProject')
            ->assertHasNoErrors()
            ->call('closeModal');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'completed_date' => now()->format('Y-m-d'),
        ]);

        Livewire::test(ProjectsForm::class)
            ->call('edit', $project->id)
            ->call('toggleCompleteProject')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'completed_date' => null,
        ]);
    }
}
