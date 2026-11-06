<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\IssuePriority;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;

/**
 * Everything the dashboard shows for one user and timeframe.
 *
 * A period runs from the start of its first day up to, and not including, the
 * day after its last. Comparing with date strings gives the same result on
 * SQLite and MySQL.
 */
final class DashboardMetrics
{
    public const array TIMEFRAMES = [30, 90, 180, 365];

    public const int DEFAULT_TIMEFRAME = 30;

    private const int LIST_LIMIT = 5;

    private const int WEEKLY_UP_TO_DAYS = 90;

    private readonly CarbonImmutable $periodEnd;

    private readonly CarbonImmutable $periodStart;

    private readonly CarbonImmutable $previousStart;

    public function __construct(
        private readonly User $user,
        private readonly int $days,
    ) {
        $this->periodEnd = Date::today()->toImmutable()->addDay();
        $this->periodStart = $this->periodEnd->subDays($days);
        $this->previousStart = $this->periodStart->subDays($days);
    }

    /**
     * @return array<string, array{value: int, previous: int, change: int|null}>
     */
    public function stats(): array
    {
        return [
            'Projects created' => $this->stat($this->projects(), 'created_at'),
            'Projects completed' => $this->stat($this->projects(), 'completed_date'),
            'Issues opened' => $this->stat($this->issues(), 'created_at'),
            'Issues resolved' => $this->stat($this->issues(), 'resolved_date'),
        ];
    }

    /**
     * Soonest due date first, with undated projects last.
     *
     * @return Collection<int, Project>
     */
    public function incompleteProjects(): Collection
    {
        return $this->projects()
            ->incomplete()
            ->orderByRaw('due_date is null')
            ->orderBy('due_date')
            ->orderBy('id')
            ->limit(self::LIST_LIMIT)
            ->get();
    }

    /**
     * @return Collection<int, Issue>
     */
    public function unresolvedIssues(): Collection
    {
        return $this->issues()
            ->unresolved()
            ->latest()
            ->orderByDesc('id')
            ->limit(self::LIST_LIMIT)
            ->get();
    }

    /**
     * Records created per bucket, with empty buckets filled with zero.
     *
     * @return list<array{label: string, range: string, projects: int, issues: int}>
     */
    public function projectsVsIssues(): array
    {
        $buckets = $this->buckets();
        $projects = $this->countByBucket($this->projects(), $buckets);
        $issues = $this->countByBucket($this->issues(), $buckets);

        return array_map(fn (array $bucket, int $index): array => [
            'label' => $bucket['label'],
            'range' => $bucket['range'],
            'projects' => $projects[$index],
            'issues' => $issues[$index],
        ], $buckets, array_keys($buckets));
    }

    /**
     * Issues opened in the period per priority, highest priority first.
     *
     * @return list<array{label: string, value: int}>
     */
    public function issuePriorities(): array
    {
        $counts = $this->issues()
            ->tap(fn (Builder $query) => $this->inPeriod($query, 'created_at', $this->periodStart, $this->periodEnd))
            ->toBase()
            ->selectRaw('priority, count(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority');

        return array_map(fn (IssuePriority $priority): array => [
            'label' => $priority->label(),
            'value' => (int) ($counts[$priority->value] ?? 0),
        ], array_reverse(IssuePriority::cases()));
    }

    /**
     * @return Builder<Project>
     */
    private function projects(): Builder
    {
        return Project::query()->ownedBy($this->user);
    }

    /**
     * @return Builder<Issue>
     */
    private function issues(): Builder
    {
        return Issue::query()->ownedBy($this->user);
    }

    /**
     * @param  Builder<*>  $query
     * @return array{value: int, previous: int, change: int|null}
     */
    private function stat(Builder $query, string $column): array
    {
        $value = $this->inPeriod($query->clone(), $column, $this->periodStart, $this->periodEnd)->count();
        $previous = $this->inPeriod($query->clone(), $column, $this->previousStart, $this->periodStart)->count();

        return [
            'value' => $value,
            'previous' => $previous,
            'change' => $previous === 0 ? null : (int) round(($value - $previous) / $previous * 100),
        ];
    }

    /**
     * @param  Builder<*>  $query
     * @return Builder<*>
     */
    private function inPeriod(Builder $query, string $column, CarbonImmutable $start, CarbonImmutable $end): Builder
    {
        return $query
            ->where($column, '>=', $start->toDateString())
            ->where($column, '<', $end->toDateString());
    }

    /**
     * @return list<array{start: CarbonImmutable, end: CarbonImmutable, label: string, range: string}>
     */
    private function buckets(): array
    {
        $bounds = $this->days <= self::WEEKLY_UP_TO_DAYS ? $this->weeklyBounds() : $this->monthlyBounds();

        return array_map(function (array $bound, int $index): array {
            [$start, $end] = $bound;

            return [
                'start' => $start,
                'end' => $end,
                'label' => $this->days <= self::WEEKLY_UP_TO_DAYS
                    ? $start->format('M j')
                    : $start->format($index === 0 || $start->month === 1 ? "M 'y" : 'M'),
                'range' => $start->format('M j').' – '.$end->subDay()->format('M j, Y'),
            ];
        }, $bounds, array_keys($bounds));
    }

    /**
     * Seven-day buckets counted back from today, so only the oldest can be short.
     *
     * @return list<array{CarbonImmutable, CarbonImmutable}>
     */
    private function weeklyBounds(): array
    {
        $bounds = [];
        $end = $this->periodEnd;

        while ($end->greaterThan($this->periodStart)) {
            $start = $end->subDays(7)->max($this->periodStart);
            array_unshift($bounds, [$start, $end]);
            $end = $start;
        }

        return $bounds;
    }

    /**
     * Calendar months clipped to the period.
     *
     * @return list<array{CarbonImmutable, CarbonImmutable}>
     */
    private function monthlyBounds(): array
    {
        $bounds = [];
        $start = $this->periodStart;

        while ($start->lessThan($this->periodEnd)) {
            $end = $start->startOfMonth()->addMonth()->min($this->periodEnd);
            $bounds[] = [$start, $end];
            $start = $end;
        }

        return $bounds;
    }

    /**
     * @param  Builder<*>  $query
     * @param  list<array{start: CarbonImmutable, end: CarbonImmutable, label: string, range: string}>  $buckets
     * @return list<int>
     */
    private function countByBucket(Builder $query, array $buckets): array
    {
        $dates = $this->inPeriod($query, 'created_at', $this->periodStart, $this->periodEnd)
            ->toBase()
            ->pluck('created_at')
            ->map(fn (mixed $date): string => CarbonImmutable::parse($date)->toDateString());

        return array_map(fn (array $bucket): int => $dates->filter(
            fn (string $date): bool => $date >= $bucket['start']->toDateString() && $date < $bucket['end']->toDateString(),
        )->count(), $buckets);
    }
}
