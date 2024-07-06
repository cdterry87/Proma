<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <h1 class="font-bold text-3xl">Dashboard</h1>
        <div>
            <x-inputs.select
                name="days"
                label="Select Timeframe"
                hide-label
                wire:model.live="days"
            >
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="180">Last 180 days</option>
                <option value="365">Last 365 days</option>
            </x-inputs.select>
        </div>
    </div>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="stats stats-vertical sm:stats-horizontal xl:stats-horizontal shadow bg-base-200 w-full">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <x-icons.projects />
                </div>
                <div class="stat-title">Projects Created</div>
                <div class="stat-value">{{ $projects }}</div>
                <div class="stat-desc">{{ $projects_change }}</div>
            </div>
            <div class="stat">
                <div class="stat-figure text-success">
                    <x-icons.success />
                </div>
                <div class="stat-title">Projects Completed</div>
                <div class="stat-value">{{ $projects_completed }}</div>
                <div class="stat-desc">{{ $projects_completed_change }}</div>
            </div>
        </div>
        <div class="stats stats-vertical sm:stats-horizontal xl:stats-horizontal shadow bg-base-200 w-full">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <x-icons.issues />
                </div>
                <div class="stat-title">Issues Opened</div>
                <div class="stat-value">{{ $issues }}</div>
                <div class="stat-desc">{{ $issues_change }}</div>
            </div>
            <div class="stat">
                <div class="stat-figure text-success">
                    <x-icons.success />
                </div>
                <div class="stat-title">Issues Resolved</div>
                <div class="stat-value">{{ $issues_resolved }}</div>
                <div class="stat-desc">{{ $issues_resolved_change }}</div>
            </div>
        </div>

        <x-layouts.card title="Incomplete Projects">
            @if ($projects_incomplete && $projects_incomplete->isNotEmpty())
                <div class="overflow-x-auto bg-base-100 rounded">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects_incomplete as $project)
                                <tr>
                                    <td class="w-full">
                                        <a
                                            href="{{ route('projects.view', $project->id) }}"
                                            class="text-primary font-bold hover:brightness-125"
                                            title="{{ $project->name }}"
                                        >{{ Str::limit($project->name, 25) }}</a>
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($project->due_date)->format('m/d/Y') ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No incomplete projects found.</p>
            @endif
        </x-layouts.card>
        <x-layouts.card title="Unresolved Issues">
            @if ($issues_unresolved && $issues_unresolved->isNotEmpty())
                <div class="overflow-x-auto bg-base-100 rounded">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Issue</th>
                                <th>Open Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($issues_unresolved as $issue)
                                <tr>
                                    <td class="w-full">
                                        <a
                                            href="{{ route('issues.view', $issue->id) }}"
                                            class="text-primary font-bold hover:brightness-125"
                                            title="{{ $issue->name }}"
                                        >{{ Str::limit($issue->name, 25) }}</a>
                                    </td>
                                    <td>{{ $issue->created_at->format('m/d/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>No unresolved issues found.</p>
            @endif
        </x-layouts.card>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-y-4 xl:gap-x-4">
        <div class="col-span-2">
            <x-layouts.card>
                <div class="h-80">
                    <livewire:livewire-line-chart
                        :key="$projectsVsIssuesChart->reactiveKey()"
                        :line-chart-model="$projectsVsIssuesChart"
                    />
                </div>
            </x-layouts.card>
        </div>
        <div class="col-span-1">
            <x-layouts.card>
                <div class="h-80">
                    <livewire:livewire-pie-chart
                        :key="$issuesPrioritiesChart->reactiveKey()"
                        :pie-chart-model="$issuesPrioritiesChart"
                    />
                </div>
            </x-layouts.card>
        </div>
    </div>
</div>
