<?php

declare(strict_types=1);

use App\Livewire\Clients\Index;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

function clientNames($component): array
{
    return $component->viewData('rows')->pluck('name')->all();
}

it('searches by name, ignoring case', function () {
    Client::factory()->for($this->user)->create(['name' => 'Acme Corp']);
    Client::factory()->for($this->user)->create(['name' => 'Globex']);

    expect(clientNames(Livewire::test(Index::class)->set('search', 'acme')))->toBe(['Acme Corp']);
});

it('treats wildcards in a search as plain text', function (string $term, array $expected) {
    Client::factory()->for($this->user)->create(['name' => '100% Co']);
    Client::factory()->for($this->user)->create(['name' => 'under_score']);
    Client::factory()->for($this->user)->create(['name' => 'wow!']);
    Client::factory()->for($this->user)->create(['name' => 'Plain']);

    expect(clientNames(Livewire::test(Index::class)->set('search', $term)))->toBe($expected);
})->with([
    ['%', ['100% Co']],
    ['_', ['under_score']],
    ['!', ['wow!']],
]);

it('keeps a search and a filter inside the owner scope [D-27]', function () {
    Client::factory()->for($this->user)->create(['name' => 'Shared name']);
    Client::factory()->create(['name' => 'Shared name']);
    Client::factory()->inactive()->create(['name' => 'Shared name']);

    $component = Livewire::test(Index::class)->set('search', 'Shared');
    expect($component->viewData('rows')->total())->toBe(1);

    $component = Livewire::test(Index::class)->set('filters.active', '0');
    expect($component->viewData('rows')->total())->toBe(0);
});

it('filters by status', function () {
    Client::factory()->for($this->user)->create(['name' => 'On']);
    Client::factory()->for($this->user)->inactive()->create(['name' => 'Off']);

    expect(clientNames(Livewire::test(Index::class)->set('filters.active', '1')))->toBe(['On'])
        ->and(clientNames(Livewire::test(Index::class)->set('filters.active', '0')))->toBe(['Off'])
        ->and(clientNames(Livewire::test(Index::class)->set('filters.active', '')))->toBe(['Off', 'On']);
});

it('ignores unknown filters and values', function () {
    Client::factory()->for($this->user)->count(2)->create();

    $component = Livewire::test(Index::class)
        ->set('filters.user_id', '999')
        ->set('filters.active', 'x');

    expect($component->viewData('rows')->total())->toBe(2);
});

it('sorts by allowed columns and toggles direction', function () {
    Client::factory()->for($this->user)->create(['name' => 'A']);
    $busy = Client::factory()->for($this->user)->create(['name' => 'B']);
    Project::factory()->forClient($busy)->create();

    $component = Livewire::test(Index::class);
    expect(clientNames($component))->toBe(['A', 'B']);

    $component->call('sortBy', 'name');
    expect(clientNames($component))->toBe(['B', 'A']);
    $component->assertSeeHtml('aria-sort="descending"');

    $component->call('sortBy', 'incomplete_projects_count');
    expect(clientNames($component))->toBe(['A', 'B']);

    $component->call('sortBy', 'incomplete_projects_count');
    expect(clientNames($component))->toBe(['B', 'A']);
});

it('ignores sort columns and directions outside the allowlist', function () {
    Client::factory()->for($this->user)->count(2)->create();

    $component = Livewire::test(Index::class)->call('sortBy', 'password');
    expect($component->viewData('activeSort'))->toBe('name');

    $component = Livewire::test(Index::class)
        ->set('sort', 'id; drop table users')
        ->set('direction', 'sideways')
        ->assertOk();

    expect($component->viewData('activeSort'))->toBe('name')
        ->and($component->viewData('activeDirection'))->toBe('asc');
});

it('paginates and limits per page to the allowed sizes', function () {
    Client::factory()->for($this->user)->count(30)->create();

    $component = Livewire::test(Index::class);
    expect($component->viewData('rows')->perPage())->toBe(10)
        ->and($component->viewData('rows')->total())->toBe(30);

    $component->set('perPage', '25');
    expect($component->viewData('rows')->perPage())->toBe(25);

    $component->set('perPage', '100000');
    expect($component->viewData('rows')->perPage())->toBe(10);
});

it('returns to the first page when the results change', function (string $property, string $value) {
    Client::factory()->for($this->user)->count(30)->create();

    $component = Livewire::test(Index::class)->call('gotoPage', 3);
    expect($component->viewData('rows')->currentPage())->toBe(3);

    $component->set($property, $value);
    expect($component->viewData('rows')->currentPage())->toBe(1);
})->with([
    ['search', 'a'],
    ['filters.active', '1'],
    ['perPage', '25'],
]);

it('marks the default sort on its heading', function () {
    Livewire::test(Index::class)->assertSeeHtml('aria-sort="ascending"');
});

it('shows an empty state', function () {
    Livewire::test(Index::class)->assertSee('No clients found.');
});

it('reads table state from the URL', function () {
    Client::factory()->for($this->user)->create(['name' => 'Match']);
    Client::factory()->for($this->user)->create(['name' => 'Other']);

    $this->get('/clients?search=Mat&sort=name&direction=desc')
        ->assertOk()
        ->assertSee('Match')
        ->assertDontSee('Other');
});

describe('export', function () {
    function downloaded($component): string
    {
        return base64_decode(data_get($component->effects, 'download.content'));
    }

    beforeEach(function () {
        $this->travelTo('2026-06-15 12:00:00');
    });

    it('exports the filtered set as CSV [D-28]', function () {
        Client::factory()->for($this->user)->create(['name' => 'Match']);
        Client::factory()->for($this->user)->create(['name' => 'Other']);
        Client::factory()->create(['name' => 'Match elsewhere']);

        $component = Livewire::test(Index::class)
            ->set('search', 'Match')
            ->call('export', 'csv')
            ->assertFileDownloaded('clients-2026-06-15.csv');

        expect(downloaded($component))
            ->toContain('Name')
            ->toContain('Match')
            ->not->toContain('Other')
            ->not->toContain('elsewhere');
    });

    it('exports an XLSX file', function () {
        Client::factory()->for($this->user)->create();

        $component = Livewire::test(Index::class)
            ->call('export', 'xlsx')
            ->assertFileDownloaded('clients-2026-06-15.xlsx');

        expect(substr(downloaded($component), 0, 2))->toBe('PK');
    });

    it('neutralizes formulas in CSV cells', function () {
        Client::factory()->for($this->user)->create(['name' => '=HYPERLINK("http://x")']);

        $component = Livewire::test(Index::class)->call('export', 'csv');

        expect(downloaded($component))->toContain("'=HYPERLINK");
    });

    it('rejects unknown formats', function () {
        Livewire::test(Index::class)->call('export', 'exe')->assertNotFound();
    });

    it('exports more rows than one chunk', function () {
        Client::factory()->for($this->user)->count(205)->create();

        $component = Livewire::test(Index::class)->call('export', 'csv');

        expect(substr_count(trim(downloaded($component)), "\n"))->toBeGreaterThanOrEqual(205);
    });
});
