<?php

declare(strict_types=1);

use App\Enums\IssuePriority;
use App\Livewire\Dashboard;
use App\Models\Client;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use App\Support\DashboardMetrics;
use App\Support\LineChart;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo('2026-06-15 12:00:00');
    $this->user = User::factory()->create();
    $this->client = Client::factory()->for($this->user)->create();
    $this->actingAs($this->user);
    $this->metrics = fn (int $days = 30) => new DashboardMetrics($this->user, $days);
});

function createdProject(Client $client, string $date, array $attributes = []): Project
{
    return Project::factory()->forClient($client)->create(['created_at' => $date, ...$attributes]);
}

function createdIssue(Client $client, string $date, array $attributes = []): Issue
{
    return Issue::factory()->forClient($client)->create(['created_at' => $date, ...$attributes]);
}

describe('stats', function () {
    it('counts the period and compares with the one before', function () {
        // The 30 day period runs from May 17 through June 15.
        createdProject($this->client, '2026-06-15 23:00:00');
        createdProject($this->client, '2026-05-17 00:00:00');
        createdProject($this->client, '2026-05-16 23:59:00');
        createdProject($this->client, '2026-04-17 00:00:00');
        createdProject($this->client, '2026-04-16 23:59:00');

        expect(($this->metrics)()->stats()['Projects created'])->toBe([
            'value' => 2,
            'previous' => 2,
            'change' => 0,
        ]);
    });

    it('computes signed change', function () {
        createdIssue($this->client, '2026-06-10');
        createdIssue($this->client, '2026-06-11');
        createdIssue($this->client, '2026-06-12');
        createdIssue($this->client, '2026-05-01');
        createdIssue($this->client, '2026-05-02');

        expect(($this->metrics)()->stats()['Issues opened']['change'])->toBe(50);

        Issue::query()->where('created_at', '>=', '2026-06-11')->delete();

        expect(($this->metrics)()->stats()['Issues opened']['change'])->toBe(-50);
    });

    it('reports no change figure when the prior period is empty', function () {
        createdIssue($this->client, '2026-06-10');

        expect(($this->metrics)()->stats()['Issues opened'])->toBe(['value' => 1, 'previous' => 0, 'change' => null]);

        Livewire::test(Dashboard::class)->assertSee('No prior data');
    });

    it('counts completions and resolutions by their own dates', function () {
        createdProject($this->client, '2025-01-01', ['completed_date' => '2026-06-15']);
        createdProject($this->client, '2026-06-01', ['completed_date' => '2026-05-16']);
        createdIssue($this->client, '2025-01-01', ['resolved_date' => '2026-05-17']);

        $stats = ($this->metrics)()->stats();

        expect($stats['Projects completed'])->toMatchArray(['value' => 1, 'previous' => 1])
            ->and($stats['Issues resolved'])->toMatchArray(['value' => 1, 'previous' => 0]);
    });

    it('ignores other users', function () {
        Project::factory()->create(['created_at' => '2026-06-10']);
        Issue::factory()->create(['created_at' => '2026-06-10']);

        $metrics = ($this->metrics)();

        expect($metrics->stats()['Projects created']['value'])->toBe(0)
            ->and($metrics->stats()['Issues opened']['value'])->toBe(0)
            ->and($metrics->incompleteProjects())->toBeEmpty()
            ->and($metrics->unresolvedIssues())->toBeEmpty()
            ->and(array_sum(array_column($metrics->issuePriorities(), 'value')))->toBe(0)
            ->and(array_sum(array_column($metrics->projectsVsIssues(), 'projects')))->toBe(0);
    });
});

describe('lists', function () {
    it('orders incomplete projects soonest first with undated last, limited to five', function () {
        Project::factory()->forClient($this->client)->create(['name' => 'Undated', 'due_date' => null]);
        foreach ([9, 3, 7, 1, 5, 11] as $days) {
            Project::factory()->forClient($this->client)->create(['name' => "In {$days}", 'due_date' => today()->addDays($days)]);
        }
        Project::factory()->forClient($this->client)->completed()->create(['name' => 'Done', 'due_date' => today()->subDay()]);

        expect(($this->metrics)()->incompleteProjects()->pluck('name')->all())
            ->toBe(['In 1', 'In 3', 'In 5', 'In 7', 'In 9']);

        Project::query()->where('name', 'like', 'In %')->delete();

        expect(($this->metrics)()->incompleteProjects()->pluck('name')->all())->toBe(['Undated']);
    });

    it('lists the latest unresolved issues, limited to five', function () {
        foreach (range(1, 6) as $day) {
            createdIssue($this->client, "2026-06-0{$day}", ['name' => "Day {$day}"]);
        }
        createdIssue($this->client, '2026-06-10', ['name' => 'Fixed', 'resolved_date' => '2026-06-11']);

        expect(($this->metrics)()->unresolvedIssues()->pluck('name')->all())
            ->toBe(['Day 6', 'Day 5', 'Day 4', 'Day 3', 'Day 2']);
    });
});

describe('trend', function () {
    it('uses weekly buckets ending today for short timeframes, filling gaps with zero', function () {
        createdProject($this->client, '2026-06-15 23:59:00');
        createdProject($this->client, '2026-06-09 00:00:00');
        createdProject($this->client, '2026-06-08 23:59:00');
        createdIssue($this->client, '2026-05-17 00:00:00');
        createdIssue($this->client, '2026-05-16 23:59:00');

        $trend = ($this->metrics)(30)->projectsVsIssues();

        expect($trend)->toHaveCount(5)
            ->and(array_column($trend, 'projects'))->toBe([0, 0, 0, 1, 2])
            ->and(array_column($trend, 'issues'))->toBe([1, 0, 0, 0, 0])
            ->and($trend[4]['range'])->toBe('Jun 9 – Jun 15, 2026')
            ->and($trend[0]['range'])->toBe('May 17 – May 18, 2026')
            ->and($trend[4]['label'])->toBe('Jun 9');
    });

    it('uses monthly buckets clipped to the timeframe for long ones', function () {
        createdProject($this->client, '2026-06-01');
        createdProject($this->client, '2026-02-15');
        createdProject($this->client, '2025-06-16');
        createdProject($this->client, '2025-06-15 23:00:00');

        $trend = ($this->metrics)(365)->projectsVsIssues();

        expect($trend)->toHaveCount(13)
            ->and($trend[0]['label'])->toBe("Jun '25")
            ->and($trend[0]['range'])->toBe('Jun 16 – Jun 30, 2025')
            ->and($trend[0]['projects'])->toBe(1)
            ->and($trend[7]['label'])->toBe("Jan '26")
            ->and($trend[8]['label'])->toBe('Feb')
            ->and($trend[8]['projects'])->toBe(1)
            ->and($trend[12]['range'])->toBe('Jun 1 – Jun 15, 2026')
            ->and($trend[12]['projects'])->toBe(1);
    });

    it('buckets every record in the timeframe exactly once', function (int $days) {
        foreach (range(0, $days - 1, 7) as $ago) {
            createdProject($this->client, today()->subDays($ago)->addHours(6)->toDateTimeString());
        }

        $metrics = ($this->metrics)($days);

        expect(array_sum(array_column($metrics->projectsVsIssues(), 'projects')))
            ->toBe($metrics->stats()['Projects created']['value']);
    })->with(DashboardMetrics::TIMEFRAMES);
});

describe('priorities', function () {
    it('counts each priority in the timeframe, highest first, with zeros', function () {
        createdIssue($this->client, '2026-06-10', ['priority' => IssuePriority::Critical]);
        createdIssue($this->client, '2026-06-11', ['priority' => IssuePriority::Low]);
        createdIssue($this->client, '2026-06-12', ['priority' => IssuePriority::Low]);
        createdIssue($this->client, '2026-01-01', ['priority' => IssuePriority::High]);

        expect(($this->metrics)()->issuePriorities())->toBe([
            ['label' => 'Critical', 'value' => 1],
            ['label' => 'High', 'value' => 0],
            ['label' => 'Medium', 'value' => 0],
            ['label' => 'Low', 'value' => 2],
        ]);
    });
});

describe('line chart', function () {
    it('picks clean integer ticks', function (int $highest, int $maximum, array $ticks) {
        $chart = new LineChart(['a', 'b'], ['s' => [0, $highest]]);

        expect($chart->maximum)->toBe($maximum)
            ->and($chart->ticks)->toBe($ticks);
    })->with([
        [1, 1, [0, 1]],
        [3, 3, [0, 1, 2, 3]],
        [7, 8, [0, 2, 4, 6, 8]],
        [18, 20, [0, 5, 10, 15, 20]],
        [130, 150, [0, 50, 100, 150]],
    ]);

    it('maps values into the plot', function () {
        $chart = new LineChart(['a', 'b', 'c'], ['s' => [0, 5, 10], 't' => [0, 0, 0]]);

        expect($chart->x(0))->toEqual(LineChart::LEFT)
            ->and($chart->x(2))->toEqual(LineChart::RIGHT)
            ->and($chart->y(0))->toEqual(LineChart::BOTTOM)
            ->and($chart->y(10))->toEqual(LineChart::TOP)
            ->and($chart->path([0, 10]))->toStartWith('M44 228 L')
            ->and($chart->isEmpty())->toBeFalse();
    });

    it('centers a single point and detects emptiness', function () {
        $chart = new LineChart(['only'], ['s' => [0]]);

        expect($chart->x(0))->toEqual((LineChart::LEFT + LineChart::RIGHT) / 2)
            ->and($chart->isEmpty())->toBeTrue();
    });

    it('thins labels on crowded axes but keeps the last', function () {
        $crowded = new LineChart(array_fill(0, 13, 'x'), ['s' => array_fill(0, 13, 1)]);
        $sparse = new LineChart(array_fill(0, 5, 'x'), ['s' => array_fill(0, 5, 1)]);

        expect($crowded->isMinorLabel(12))->toBeFalse()
            ->and($crowded->isMinorLabel(11))->toBeTrue()
            ->and($crowded->isMinorLabel(9))->toBeFalse()
            ->and($sparse->isMinorLabel(1))->toBeFalse();
    });
});

describe('page', function () {
    it('requires authentication', function () {
        auth()->logout();

        $this->get('/')->assertRedirect('/login');
    });

    it('shows each empty state on an empty account', function () {
        $this->get('/')
            ->assertOk()
            ->assertSeeLivewire(Dashboard::class)
            ->assertSee('No incomplete projects found.')
            ->assertSee('No unresolved issues found.')
            ->assertSee('Not enough data.')
            ->assertDontSeeHtml('chart__svg');
    });

    it('decides each chart on its own data', function () {
        createdProject($this->client, '2026-06-10');

        // A project alone draws the trend but not the priorities.
        Livewire::test(Dashboard::class)
            ->assertSee('Not enough data.')
            ->assertSee('Projects and issues created per period')
            ->assertDontSee('Issues opened by priority');
    });

    it('renders accessible charts, lists, and links', function () {
        $project = createdProject($this->client, '2026-06-10', ['name' => 'Launch', 'due_date' => '2026-06-01']);
        $issue = createdIssue($this->client, '2026-06-10', ['name' => 'Broken', 'priority' => IssuePriority::High]);

        $this->get('/')
            ->assertSee('Launch')
            ->assertSee('Broken')
            ->assertSee(route('projects.show', $project))
            ->assertSee(route('issues.show', $issue))
            ->assertSee('Due Jun 1, 2026')
            ->assertSee('Opened Jun 10, 2026')
            ->assertSeeHtml('<caption>Projects and issues created per period</caption>')
            ->assertSeeHtml('<caption>Issues opened by priority</caption>')
            ->assertSeeHtml('aria-label="High: 1, 100 percent"')
            ->assertDontSeeHtml(' style=');
    });

    it('escapes names in charts and lists', function () {
        createdProject($this->client, '2026-06-10', ['name' => '<img src=x>']);

        $this->get('/')->assertDontSeeHtml('<img src=x>')->assertSee('<img src=x>');
    });

    it('changes timeframe and accepts only allowed values', function () {
        createdProject($this->client, '2026-03-01');

        $component = Livewire::test(Dashboard::class);
        expect($component->viewData('stats')['Projects created']['value'])->toBe(0)
            ->and($component->viewData('activeDays'))->toBe(30);

        $component->set('days', '180');
        expect($component->viewData('stats')['Projects created']['value'])->toBe(1)
            ->and($component->viewData('activeDays'))->toBe(180)
            ->and(count($component->viewData('trend')))->toBe(7);

        $component->set('days', '99999');
        expect($component->viewData('activeDays'))->toBe(30);

        $this->get('/?days=90')->assertOk()->assertSee('vs previous 90 days', false);
        $this->get('/?days=abc')->assertOk();
    });
});
