@use('App\Support\DateFormatter')

<div class="page">
    <x-page-header title="Dashboard">
        <x-slot:actions>
            <fieldset class="timeframe">
                <legend>Timeframe</legend>
                @foreach ($timeframes as $timeframe)
                    <label class="timeframe__option" wire:key="timeframe-{{ $timeframe }}">
                        <input type="radio" name="days" value="{{ $timeframe }}" wire:model.live="days" @checked($timeframe === $activeDays)>
                        {{ $timeframe }} days
                    </label>
                @endforeach
            </fieldset>
        </x-slot:actions>
    </x-page-header>

    <div class="stat-grid">
        @foreach ($stats as $label => $stat)
            <x-stat
                :label="$label"
                :value="$stat['value']"
                :hint="$stat['change'] === null
                    ? 'No prior data'
                    : ($stat['change'] > 0 ? '+' : '').$stat['change'].'% vs previous '.$activeDays.' days'"
                wire:key="stat-{{ $label }}"
            />
        @endforeach
    </div>

    <div class="split split--chart">
        <x-card title="Projects vs issues" subtitle="Records created in the timeframe">
            @if ($trendChart->isEmpty())
                <p class="muted">Not enough data.</p>
            @else
                <x-chart.line
                    :chart="$trendChart"
                    :ranges="array_column($trend, 'range')"
                    caption="Projects and issues created per period"
                    wire:key="trend-{{ $activeDays }}"
                />
            @endif
        </x-card>

        <x-card title="Issue priorities" subtitle="Issues opened in the timeframe">
            @if (array_sum(array_column($priorities, 'value')) === 0)
                <p class="muted">Not enough data.</p>
            @else
                <x-chart.bars :rows="$priorities" caption="Issues opened by priority" wire:key="priorities-{{ $activeDays }}" />
            @endif
        </x-card>
    </div>

    <div class="split">
        <x-card title="Incomplete projects" subtitle="Soonest due first">
            <x-slot:actions>
                <a href="{{ route('projects.index', ['filters' => ['completed' => 'no']]) }}" wire:navigate>View all</a>
            </x-slot:actions>
            @if ($incompleteProjects->isEmpty())
                <p class="muted">No incomplete projects found.</p>
            @else
                <ul class="related-list" role="list">
                    @foreach ($incompleteProjects as $project)
                        <li wire:key="project-{{ $project->uuid }}">
                            <a href="{{ route('projects.show', $project) }}" wire:navigate>{{ $project->name }}</a>
                            @if ($project->isOverdue())
                                <x-badge variant="error">Due {{ DateFormatter::format($project->due_date) }}</x-badge>
                            @else
                                <span class="muted">{{ $project->due_date ? 'Due '.DateFormatter::format($project->due_date) : 'No due date' }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        <x-card title="Unresolved issues" subtitle="Latest opened">
            <x-slot:actions>
                <a href="{{ route('issues.index', ['filters' => ['resolved' => 'no']]) }}" wire:navigate>View all</a>
            </x-slot:actions>
            @if ($unresolvedIssues->isEmpty())
                <p class="muted">No unresolved issues found.</p>
            @else
                <ul class="related-list" role="list">
                    @foreach ($unresolvedIssues as $issue)
                        <li wire:key="issue-{{ $issue->uuid }}">
                            <a href="{{ route('issues.show', $issue) }}" wire:navigate>{{ $issue->name }}</a>
                            <span class="muted">Opened {{ DateFormatter::format($issue->created_at) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>
</div>
