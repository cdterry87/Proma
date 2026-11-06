<?php

declare(strict_types=1);

use App\Enums\IssuePriority;
use App\Livewire\Issues\Index;
use App\Livewire\Issues\Show;
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
    $this->project = Project::factory()->forClient($this->client)->create(['name' => 'Launch']);
    $this->actingAs($this->user);
});

function validIssue(int $clientId, array $overrides = []): array
{
    return [
        'form.name' => 'Broken',
        'form.description' => 'It broke',
        'form.priority' => '3',
        'form.client_id' => (string) $clientId,
        ...$overrides,
    ];
}

describe('list', function () {
    it('lists only the user\'s issues and handles a missing project', function () {
        Issue::factory()->forClient($this->client)->create(['name' => 'Mine', 'priority' => IssuePriority::Critical]);
        Issue::factory()->create(['name' => 'Theirs']);

        $this->get('/issues')
            ->assertOk()
            ->assertSeeLivewire(Index::class)
            ->assertSee('Mine')
            ->assertSee('Critical')
            ->assertSee('None')
            ->assertSee('Unresolved')
            ->assertDontSee('Theirs');
    });

    it('filters by priority, status, and client', function () {
        $otherClient = Client::factory()->for($this->user)->create();
        Issue::factory()->forClient($this->client)->create(['name' => 'High', 'priority' => IssuePriority::High]);
        Issue::factory()->forClient($this->client)->resolved()->create(['name' => 'Low done', 'priority' => IssuePriority::Low]);
        Issue::factory()->forClient($otherClient)->create(['name' => 'Elsewhere', 'priority' => IssuePriority::Low]);

        $names = fn ($component) => $component->viewData('rows')->pluck('name')->sort()->values()->all();

        expect($names(Livewire::test(Index::class)->set('filters.priority', '3')))->toBe(['High'])
            ->and($names(Livewire::test(Index::class)->set('filters.resolved', 'yes')))->toBe(['Low done'])
            ->and($names(Livewire::test(Index::class)->set('filters.resolved', 'no')))->toBe(['Elsewhere', 'High'])
            ->and($names(Livewire::test(Index::class)->set('filters.client', $otherClient->uuid)))->toBe(['Elsewhere'])
            ->and($names(Livewire::test(Index::class)->set('filters.priority', '9')))->toBe(['Elsewhere', 'High', 'Low done']);
    });

    it('sorts by priority and project name with empty projects', function () {
        Issue::factory()->forClient($this->client)->create(['name' => 'Low', 'priority' => IssuePriority::Low]);
        Issue::factory()->forProject($this->project)->create(['name' => 'Critical', 'priority' => IssuePriority::Critical]);

        $component = Livewire::test(Index::class)->call('sortBy', 'priority');
        expect($component->viewData('rows')->pluck('name')->all())->toBe(['Low', 'Critical']);

        $component->call('sortBy', 'priority');
        expect($component->viewData('rows')->pluck('name')->all())->toBe(['Critical', 'Low']);

        expect(Livewire::test(Index::class)->call('sortBy', 'project_name')->viewData('rows')->total())->toBe(2);
    });

    it('exports with a blank project', function () {
        Issue::factory()->forClient($this->client)->create(['name' => 'Solo', 'priority' => IssuePriority::High]);

        $component = Livewire::test(Index::class)->call('export', 'csv')->assertFileDownloaded('issues-2026-06-15.csv');

        expect(base64_decode(data_get($component->effects, 'download.content')))->toContain('Solo')->toContain('High');
    });
});

describe('create', function () {
    it('creates an issue owned by the user', function () {
        Livewire::test(Index::class)
            ->set(validIssue($this->client->id))
            ->set('form.project_id', (string) $this->project->id)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-dialog', name: 'issue-form');

        expect(Issue::query()->sole())
            ->user_id->toBe($this->user->id)
            ->project_id->toBe($this->project->id)
            ->priority->toBe(IssuePriority::High)
            ->resolved_date->toBeNull();
    });

    it('allows no project', function () {
        Livewire::test(Index::class)->set(validIssue($this->client->id))->call('save')->assertHasNoErrors();

        expect(Issue::query()->sole()->project_id)->toBeNull();
    });

    it('validates fields', function (array $overrides, string $error) {
        Livewire::test(Index::class)
            ->set(validIssue($this->client->id, $overrides))
            ->call('save')
            ->assertHasErrors([$error]);

        expect(Issue::query()->count())->toBe(0);
    })->with([
        'missing name' => [['form.name' => ''], 'form.name'],
        'missing description' => [['form.description' => ''], 'form.description'],
        'missing priority' => [['form.priority' => ''], 'form.priority'],
        'invalid priority' => [['form.priority' => '9'], 'form.priority'],
        'missing client' => [['form.client_id' => ''], 'form.client_id'],
    ]);

    it('rejects another user\'s client', function () {
        Livewire::test(Index::class)
            ->set(validIssue(Client::factory()->create()->id))
            ->call('save')
            ->assertHasErrors(['form.client_id']);
    });

    it('rejects a project of another client or user', function (Closure $project) {
        $id = $project($this->user)->id;

        Livewire::test(Index::class)
            ->set(validIssue($this->client->id))
            ->set('form.project_id', (string) $id)
            ->call('save')
            ->assertHasErrors(['form.project_id']);

        expect(Issue::query()->count())->toBe(0);
    })->with([
        'other client' => fn (User $user) => Project::factory()->forClient(Client::factory()->for($user)->create())->create(),
        'other user' => fn (User $user) => Project::factory()->create(),
    ]);

    it('clears the project when the client changes', function () {
        $otherClient = Client::factory()->for($this->user)->create();

        Livewire::test(Index::class)
            ->set('form.client_id', (string) $this->client->id)
            ->set('form.project_id', (string) $this->project->id)
            ->set('form.client_id', (string) $otherClient->id)
            ->assertSet('form.project_id', '');
    });

    it('lists the selected client\'s incomplete projects', function () {
        Project::factory()->forClient($this->client)->completed()->create(['name' => 'Finished']);
        Project::factory()->create(['name' => 'Foreign']);

        $component = Livewire::test(Index::class)->assertViewHas('projectOptions', fn ($options) => $options->isEmpty());

        $component->set('form.client_id', (string) $this->client->id)
            ->assertViewHas('projectOptions', fn ($options) => $options->values()->all() === ['Launch']);
    });

    it('shows no foreign projects for a tampered client', function () {
        $foreign = Project::factory()->create();

        Livewire::test(Index::class)
            ->set('form.client_id', (string) $foreign->client_id)
            ->assertViewHas('projectOptions', fn ($options) => $options->isEmpty());
    });
});

describe('detail', function () {
    beforeEach(function () {
        $this->issue = Issue::factory()->forClient($this->client)->create(['name' => 'Broken', 'priority' => IssuePriority::High]);
    });

    it('renders with its tasks manager and a missing project', function () {
        $this->get("/issues/{$this->issue->uuid}")
            ->assertOk()
            ->assertSeeLivewire(Show::class)
            ->assertSeeLivewire(Manager::class)
            ->assertSee('Broken')
            ->assertSee('High')
            ->assertSee('None');
    });

    it('returns 404 for another user\'s issue', function () {
        $this->get('/issues/'.Issue::factory()->create()->uuid)->assertNotFound();
    });

    it('resolves and unresolves', function () {
        $component = Livewire::test(Show::class, ['issue' => $this->issue])
            ->set('form.resolved', true)
            ->call('save')
            ->assertHasNoErrors();

        expect($this->issue->fresh()->resolved_date->toDateString())->toBe('2026-06-15');

        $this->travel(2)->days();
        $component->set('form.name', 'Renamed')->call('save');
        expect($this->issue->fresh()->resolved_date->toDateString())->toBe('2026-06-15');

        $component->set('form.resolved', false)->call('save');
        expect($this->issue->fresh()->resolved_date)->toBeNull();
    });

    it('clears the project when the client changes and rejects a mismatch', function () {
        $this->issue->update(['project_id' => $this->project->id]);
        $otherClient = Client::factory()->for($this->user)->create();

        $component = Livewire::test(Show::class, ['issue' => $this->issue->fresh()])
            ->assertSet('form.project_id', (string) $this->project->id)
            ->set('form.client_id', (string) $otherClient->id)
            ->assertSet('form.project_id', '');

        $component->set('form.project_id', (string) $this->project->id)
            ->call('save')
            ->assertHasErrors(['form.project_id']);

        expect($this->issue->fresh()->client_id)->toBe($this->client->id);
    });

    it('keeps a completed project and inactive client available when editing', function () {
        $this->project->update(['completed_date' => '2026-06-01']);
        $this->client->update(['active' => false]);
        $this->issue->update(['project_id' => $this->project->id]);

        Livewire::test(Show::class, ['issue' => $this->issue->fresh()])
            ->assertViewHas('clientOptions', fn ($options) => $options->has($this->client->id))
            ->assertViewHas('projectOptions', fn ($options) => $options->has($this->project->id))
            ->call('save')
            ->assertHasNoErrors();
    });

    it('deletes with its tasks', function () {
        Task::factory()->forIssue($this->issue)->create();

        Livewire::test(Show::class, ['issue' => $this->issue])
            ->assertSee('its tasks (1)')
            ->call('delete')
            ->assertRedirect('/issues');

        expect(Issue::query()->count())->toBe(0)
            ->and(Task::query()->count())->toBe(0);
    });

    it('denies another user', function (string $method) {
        $component = Livewire::test(Show::class, ['issue' => $this->issue]);

        $this->actingAs(User::factory()->create());

        $component->call($method)->assertForbidden();

        expect($this->issue->fresh())->not->toBeNull();
    })->with(['save', 'delete']);
});
