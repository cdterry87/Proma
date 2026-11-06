<?php

declare(strict_types=1);

use App\Livewire\Projects\Index;
use App\Livewire\Projects\Show;
use App\Livewire\Tasks\Manager;
use App\Models\Client;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo('2026-06-15 12:00:00');
    $this->user = User::factory()->create();
    $this->client = Client::factory()->for($this->user)->create(['name' => 'Acme']);
    $this->actingAs($this->user);
});

function validProject(int $clientId, array $overrides = []): array
{
    return [
        'form.name' => 'Launch',
        'form.description' => 'Launch the thing',
        'form.client_id' => (string) $clientId,
        'form.start_date' => '2026-06-01',
        'form.due_date' => '2026-07-01',
        ...$overrides,
    ];
}

function projectNames($component): array
{
    return $component->viewData('rows')->pluck('name')->sort()->values()->all();
}

describe('list', function () {
    it('requires authentication', function () {
        auth()->logout();

        $this->get('/projects')->assertRedirect('/login');
    });

    it('lists only the user\'s projects with labels for missing dates', function () {
        Project::factory()->forClient($this->client)->create([
            'name' => 'Mine', 'start_date' => null, 'due_date' => null,
        ]);
        Project::factory()->create(['name' => 'Theirs']);

        $this->get('/projects')
            ->assertOk()
            ->assertSeeLivewire(Index::class)
            ->assertSee('Mine')
            ->assertSee('Acme')
            ->assertSee('Not started')
            ->assertSee('Not due')
            ->assertSee('Incomplete')
            ->assertDontSee('Theirs');
    });

    it('searches and sorts by client name', function () {
        $other = Client::factory()->for($this->user)->create(['name' => 'Zeta']);
        Project::factory()->forClient($other)->create(['name' => 'Alpha']);
        Project::factory()->forClient($this->client)->create(['name' => 'Beta']);

        expect(projectNames(Livewire::test(Index::class)->set('search', 'alp')))->toBe(['Alpha'])
            ->and(Livewire::test(Index::class)->call('sortBy', 'client_name')->viewData('rows')->pluck('name')->all())->toBe(['Beta', 'Alpha']);
    });

    it('filters by date status', function (string $filter, string $value, array $expected) {
        Project::factory()->forClient($this->client)->create(['name' => 'Started today', 'start_date' => '2026-06-15', 'due_date' => '2026-06-15']);
        Project::factory()->forClient($this->client)->create(['name' => 'Future', 'start_date' => '2026-06-16', 'due_date' => '2026-06-20']);
        Project::factory()->forClient($this->client)->create(['name' => 'Undated', 'start_date' => null, 'due_date' => null]);
        Project::factory()->forClient($this->client)->create(['name' => 'Late', 'start_date' => '2026-06-01', 'due_date' => '2026-06-14']);
        Project::factory()->forClient($this->client)->create(['name' => 'Late but done', 'start_date' => '2026-06-01', 'due_date' => '2026-06-14', 'completed_date' => '2026-06-10']);

        expect(projectNames(Livewire::test(Index::class)->set("filters.{$filter}", $value)))->toBe($expected);
    })->with([
        'started' => ['started', 'yes', ['Late', 'Late but done', 'Started today']],
        'not started' => ['started', 'no', ['Future', 'Undated']],
        'past due' => ['due', 'yes', ['Late']],
        'not due' => ['due', 'no', ['Future', 'Late but done', 'Started today', 'Undated']],
        'completed' => ['completed', 'yes', ['Late but done']],
        'incomplete' => ['completed', 'no', ['Future', 'Late', 'Started today', 'Undated']],
    ]);

    it('keeps an OR filter inside the owner scope', function () {
        Project::factory()->create(['name' => 'Theirs undated', 'start_date' => null, 'due_date' => null]);
        Project::factory()->forClient($this->client)->create(['name' => 'Mine', 'start_date' => null, 'due_date' => null]);

        expect(projectNames(Livewire::test(Index::class)->set('filters.started', 'no')))->toBe(['Mine'])
            ->and(projectNames(Livewire::test(Index::class)->set('filters.due', 'no')))->toBe(['Mine']);
    });

    it('filters by client, offering only the user\'s clients', function () {
        $otherClient = Client::factory()->for($this->user)->create();
        Project::factory()->forClient($this->client)->create(['name' => 'A']);
        Project::factory()->forClient($otherClient)->create(['name' => 'B']);
        $foreign = Project::factory()->create(['name' => 'Foreign']);

        $this->get('/projects?filters[client]='.$this->client->uuid)->assertSee('A')->assertDontSee('Foreign');

        expect(projectNames(Livewire::test(Index::class)->set('filters.client', $this->client->uuid)))->toBe(['A'])
            ->and(projectNames(Livewire::test(Index::class)->set('filters.client', $foreign->client->uuid)))->toBe(['A', 'B']);
    });

    it('exports the filtered set', function () {
        Project::factory()->forClient($this->client)->create(['name' => 'Done', 'completed_date' => '2026-06-10']);
        Project::factory()->forClient($this->client)->create(['name' => 'Open']);

        $component = Livewire::test(Index::class)
            ->set('filters.completed', 'yes')
            ->call('export', 'csv')
            ->assertFileDownloaded('projects-2026-06-15.csv');

        expect(base64_decode(data_get($component->effects, 'download.content')))
            ->toContain('Done')->toContain('Acme')->toContain('2026-06-10')
            ->not->toContain('Open');
    });
});

describe('create', function () {
    it('creates a project owned by the user', function () {
        Livewire::test(Index::class)
            ->set(validProject($this->client->id))
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-dialog', name: 'project-form')
            ->assertSet('form.name', '');

        $project = Project::query()->sole();

        expect($project->user_id)->toBe($this->user->id)
            ->and($project->client_id)->toBe($this->client->id)
            ->and($project->due_date->toDateString())->toBe('2026-07-01')
            ->and($project->completed_date)->toBeNull();
    });

    it('stores empty dates as null', function () {
        Livewire::test(Index::class)
            ->set(validProject($this->client->id, ['form.start_date' => '', 'form.due_date' => '']))
            ->call('save')
            ->assertHasNoErrors();

        expect(Project::query()->sole())
            ->start_date->toBeNull()
            ->due_date->toBeNull();
    });

    it('validates fields', function (array $overrides, string $error) {
        Livewire::test(Index::class)
            ->set(validProject($this->client->id, $overrides))
            ->call('save')
            ->assertHasErrors([$error])
            ->assertNotDispatched('close-dialog');

        expect(Project::query()->count())->toBe(0);
    })->with([
        'missing name' => [['form.name' => ''], 'form.name'],
        'missing description' => [['form.description' => ''], 'form.description'],
        'missing client' => [['form.client_id' => ''], 'form.client_id'],
        'bad date' => [['form.start_date' => 'nope'], 'form.start_date'],
        'due before start' => [['form.due_date' => '2026-05-01'], 'form.due_date'],
    ]);

    it('rejects another user\'s client', function () {
        Livewire::test(Index::class)
            ->set(validProject(Client::factory()->create()->id))
            ->call('save')
            ->assertHasErrors(['form.client_id']);

        expect(Project::query()->count())->toBe(0);
    });

    it('offers only active clients', function () {
        Client::factory()->for($this->user)->inactive()->create(['name' => 'Dormant']);
        Client::factory()->create(['name' => 'Foreign']);

        expect(Livewire::test(Index::class)->viewData('clientOptions')->values()->all())->toBe(['Acme']);
    });

    it('asks for a client when there are none', function () {
        $this->client->update(['active' => false]);

        Livewire::test(Index::class)->assertSee('You need an active client first.');
    });
});

describe('detail', function () {
    beforeEach(function () {
        $this->project = Project::factory()->forClient($this->client)->create([
            'name' => 'Launch', 'start_date' => null, 'due_date' => '2026-06-01',
        ]);
    });

    it('renders with its tasks manager, labels, and counts', function () {
        Issue::factory()->forProject($this->project)->create(['name' => 'Open issue']);
        Issue::factory()->forProject($this->project)->resolved()->create(['name' => 'Fixed issue']);
        Task::factory()->forProject($this->project)->completed()->create();

        $this->get("/projects/{$this->project->uuid}")
            ->assertOk()
            ->assertSeeLivewire(Show::class)
            ->assertSeeLivewire(Manager::class)
            ->assertSee('Past due')
            ->assertSee('Not started')
            ->assertSee('Open issue')
            ->assertDontSee('Fixed issue');

        Livewire::test(Show::class, ['project' => $this->project])
            ->assertViewHas('resolvedIssueCount', 1)
            ->assertViewHas('incompleteTaskCount', 0)
            ->assertViewHas('completedTaskCount', 1);
    });

    it('returns 404 for another user\'s project', function () {
        $this->get('/projects/'.Project::factory()->create()->uuid)->assertNotFound();
    });

    it('updates details and completes without moving the date later', function () {
        $component = Livewire::test(Show::class, ['project' => $this->project])
            ->assertSet('form.due_date', '2026-06-01')
            ->set('form.name', 'Renamed')
            ->set('form.completed', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-dialog', name: 'project-form');

        $project = $this->project->fresh();

        expect($project->name)->toBe('Renamed')
            ->and($project->completed_date->toDateString())->toBe('2026-06-15')
            ->and($project->user_id)->toBe($this->user->id);

        $this->travel(3)->days();

        $component->set('form.name', 'Again')->call('save');

        expect($this->project->fresh()->completed_date->toDateString())->toBe('2026-06-15');

        $component->set('form.completed', false)->call('save');

        expect($this->project->fresh()->completed_date)->toBeNull();
    });

    it('moves the project\'s issues when its client changes', function () {
        $issue = Issue::factory()->forProject($this->project)->create();
        $unrelated = Issue::factory()->forClient($this->client)->create();
        $newClient = Client::factory()->for($this->user)->create();

        Livewire::test(Show::class, ['project' => $this->project])
            ->set('form.client_id', (string) $newClient->id)
            ->call('save')
            ->assertHasNoErrors();

        expect($issue->fresh()->client_id)->toBe($newClient->id)
            ->and($unrelated->fresh()->client_id)->toBe($this->client->id);
    });

    it('keeps an inactive client available when editing', function () {
        $this->client->update(['active' => false]);

        Livewire::test(Show::class, ['project' => $this->project])
            ->assertViewHas('clientOptions', fn ($options) => $options->has($this->client->id));
    });

    it('restores saved values when the dialog closes', function () {
        Livewire::test(Show::class, ['project' => $this->project])
            ->set('form.name', 'Unsaved')
            ->dispatch('dialog-closed', name: 'project-form')
            ->assertSet('form.name', 'Launch');
    });

    it('deletes with its issues and tasks', function () {
        Task::factory()->forProject($this->project)->create();
        $issue = Issue::factory()->forProject($this->project)->create();
        Task::factory()->forIssue($issue)->create();

        Livewire::test(Show::class, ['project' => $this->project])
            ->assertSee('its tasks (1), issues (1)')
            ->call('delete')
            ->assertRedirect('/projects');

        expect(Project::query()->count())->toBe(0)
            ->and(Issue::query()->count())->toBe(0)
            ->and(Task::query()->count())->toBe(0);
    });

    it('locks the record', function () {
        Livewire::test(Show::class, ['project' => $this->project])->set('project', Project::factory()->create());
    })->throws('Cannot update locked property');

    it('denies another user', function (string $method) {
        $component = Livewire::test(Show::class, ['project' => $this->project]);

        $this->actingAs(User::factory()->create());

        $component->call($method)->assertForbidden();

        expect($this->project->fresh())->not->toBeNull();
    })->with(['save', 'delete']);
});
