<?php

namespace App\Livewire;

use App\Models\Issue;
use App\Models\Project;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Asantibanez\LivewireCharts\Models\PieChartModel;

class Home extends Component
{
    public $days = 30;

    private $chartJsonConfig = [
        'title' => [
            'style' => [
                'fontSize' => '16px',
                'color' => '#898989'
            ]
        ],
        'legend' => [
            'fontSize' => '14px',
            'fontWeight' => 'bold',
            'labels' => [
                'colors' => [
                    '#898989'
                ]
            ]
        ],
        'tooltip' => [
            'theme' => 'dark',
        ],
        'xaxis' => [
            'labels' => [
                'style' => [
                    'fontSize' => '12px',
                    'colors' => '#898989'
                ]
            ]
        ],
        'yaxis' => [
            'labels' => [
                'style' => [
                    'fontSize' => '12px',
                    'colors' => '#898989'
                ]
            ]
        ]
    ];

    public function render()
    {
        // Get number of projects created in the last 30 days
        $projects = Project::where('user_id', auth()->id())->where('created_at', '>=', now()->subDays($this->days))->count();

        // Get previous number of projects created in the 30 days before the last 30 days
        $projects_previous = Project::where('user_id', auth()->id())->where('created_at', '>=', now()->subDays($this->days * 2))->where('created_at', '<', now()->subDays($this->days))->count();

        // Calculate % change in projects created
        $projects_change = $projects_previous ? round((($projects - $projects_previous) / $projects_previous) * 100, 1) : 0;

        if ($projects_change == 0) {
            $projects_change = $projects_change . '% Change';
        } elseif ($projects_change > 0) {
            $projects_change = '+' . $projects_change . '% Increase';
        } elseif ($projects_change < 0) {
            $projects_change = $projects_change . '% Decrease';
        }

        // Get number of projects completed in the last 30 days
        $projects_completed = Project::where('user_id', auth()->id())->where('completed_date', '>=', now()->subDays($this->days))->count();

        // Get previous number of projects completed in the 30 days before the last 30 days
        $projects_completed_previous = Project::where('user_id', auth()->id())->where('completed_date', '>=', now()->subDays($this->days * 2))->where('completed_date', '<', now()->subDays($this->days))->count();

        // Calculate % change in projects completed
        $projects_completed_change = $projects_completed_previous ? round((($projects_completed - $projects_completed_previous) / $projects_completed_previous) * 100, 1) : 0;

        if ($projects_completed_change == 0) {
            $projects_completed_change = $projects_completed_change . '% Change';
        } elseif ($projects_completed_change > 0) {
            $projects_completed_change = '+' . $projects_completed_change . '% Increase';
        } elseif ($projects_completed_change < 0) {
            $projects_completed_change = $projects_completed_change . '% Decrease';
        }

        // Get last 5 incomplete projects
        $projects_incomplete = Project::where('user_id', auth()->id())->whereNull('completed_date')->orderBy('due_date', 'desc')->limit(5)->get();

        // Get number of issues created in the last 30 days
        $issues = Issue::where('user_id', auth()->id())->where('created_at', '>=', now()->subDays($this->days))->count();

        // Get previous number of issues created in the 30 days before the last 30 days
        $issues_previous = Issue::where('user_id', auth()->id())->where('created_at', '>=', now()->subDays($this->days * 2))->where('created_at', '<', now()->subDays($this->days))->count();

        // Calculate % change in issues created
        $issues_change = $issues_previous ? round((($issues - $issues_previous) / $issues_previous) * 100, 1) : 0;

        if ($issues_change == 0) {
            $issues_change = $issues_change . '% Change';
        } elseif ($issues_change > 0) {
            $issues_change = '+' . $issues_change . '% Increase';
        } elseif ($issues_change < 0) {
            $issues_change = $issues_change . '% Decrease';
        }

        // Get number of issues resolved in the last 30 days
        $issues_resolved = Issue::where('user_id', auth()->id())->where('resolved_date', '>=', now()->subDays($this->days))->count();

        // Get previous number of issues resolved in the 30 days before the last 30 days
        $issues_resolved_previous = Issue::where('user_id', auth()->id())->where('resolved_date', '>=', now()->subDays($this->days * 2))->where('resolved_date', '<', now()->subDays($this->days))->count();

        // Calculate % change in issues resolved
        $issues_resolved_change = $issues_resolved_previous ? round((($issues_resolved - $issues_resolved_previous) / $issues_resolved_previous) * 100, 1) : 0;

        if ($issues_resolved_change == 0) {
            $issues_resolved_change = $issues_resolved_change . '% Change';
        } elseif ($issues_resolved_change > 0) {
            $issues_resolved_change = '+' . $issues_resolved_change . '% Increase';
        } elseif ($issues_resolved_change < 0) {
            $issues_resolved_change = $issues_resolved_change . '% Decrease';
        }

        // Get last 5 unresolved issues
        $issues_unresolved = Issue::where('user_id', auth()->id())->whereNull('resolved_date')->orderBy('created_at', 'desc')->limit(5)->get();

        // Get issue priorities chart
        $issuesPrioritiesChart = $this->getIssuesPrioritiesChart();

        // Get projects vs issues chart
        $projectsVsIssuesChart = $this->getProjectsVsIssuesChart();

        return view('livewire.home', [
            'projects' => $projects,
            'projects_completed' => $projects_completed,
            'projects_change' => $projects_change,
            'projects_completed_change' => $projects_completed_change,
            'issues' => $issues,
            'issues_resolved' => $issues_resolved,
            'issues_change' => $issues_change,
            'issues_resolved_change' => $issues_resolved_change,
            'projects_incomplete' => $projects_incomplete,
            'issues_unresolved' => $issues_unresolved,
            'issuesPrioritiesChart' => $issuesPrioritiesChart,
            'projectsVsIssuesChart' => $projectsVsIssuesChart
        ]);
    }

    private function getIssuesPrioritiesChart()
    {
        $issues = Issue::query()
            ->where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays($this->days))
            ->groupBy('priority')
            ->orderBy('priority')
            ->select('priority', DB::raw('count(*) as total'))
            ->get()
            ->mapWithKeys(fn ($issue) => [$issue->priority => $issue->total])
            ->toArray();

        $lowCount = $issues[1] ?? 0;
        $mediumCount = $issues[2] ?? 0;
        $highCount = $issues[3] ?? 0;
        $criticalCount = $issues[4] ?? 0;

        return (new PieChartModel())
            ->setTitle('Issue Priorities (Last ' . $this->days . ' Days)')
            ->withDataLabels()
            ->addSlice('Low', $lowCount, '#047857')
            ->addSlice('Medium', $mediumCount, '#1d4ed8')
            ->addSlice('High', $highCount, '#6d28d9')
            ->addSlice('Critical', $criticalCount, '#be123c')
            ->setJsonConfig($this->chartJsonConfig);
    }

    private function getProjectsVsIssuesChart()
    {
        // Get projects created in the last 30 days grouped by date
        $projects = Project::query()
            ->selectRaw("
                count(id) AS count,
                DATE_FORMAT(created_at, '%M %Y') AS full_date,
                DATE_FORMAT(created_at, '%y%m') AS order_by_date
            ")
            ->groupBy('full_date', 'order_by_date')
            ->orderBy('order_by_date')
            ->where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays($this->days))
            ->get()
            ->mapWithKeys(fn ($project) => [$project->full_date => $project->count])
            ->toArray();

        $issues = Issue::query()
            ->selectRaw("
                count(id) AS count,
                DATE_FORMAT(created_at, '%M %Y') AS full_date,
                DATE_FORMAT(created_at, '%y%m') AS order_by_date
            ")
            ->groupBy('full_date', 'order_by_date')
            ->orderBy('order_by_date')
            ->where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays($this->days))
            ->get()
            ->mapWithKeys(fn ($issue) => [$issue->full_date => $issue->count])
            ->toArray();

        // Merge array keys to get x axis labels
        $labels = array_unique(array_merge(array_keys($projects), array_keys($issues)));

        $chart = (new LineChartModel())
            ->setTitle('Projects vs Issues (Last ' . $this->days . ' Days)')
            ->withDataLabels()
            ->multiLine()
            ->setJsonConfig($this->chartJsonConfig);

        foreach ($projects as $label => $value) {
            $chart->addSeriesPoint('Projects', $label, $value);
        }

        foreach ($issues as $label => $value) {
            $chart->addSeriesPoint('Issues', $label, $value);
        }

        return $chart;
    }
}
