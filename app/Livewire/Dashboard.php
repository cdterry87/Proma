<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\HasCurrentUser;
use App\Support\DashboardMetrics;
use App\Support\LineChart;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    use HasCurrentUser;

    #[Url(except: '30')]
    public string $days = '30';

    public function render(): View
    {
        $days = $this->activeDays();
        $metrics = new DashboardMetrics($this->currentUser(), $days);
        $trend = $metrics->projectsVsIssues();

        return view('livewire.dashboard', [
            'activeDays' => $days,
            'timeframes' => DashboardMetrics::TIMEFRAMES,
            'stats' => $metrics->stats(),
            'incompleteProjects' => $metrics->incompleteProjects(),
            'unresolvedIssues' => $metrics->unresolvedIssues(),
            'trend' => $trend,
            'trendChart' => new LineChart(array_column($trend, 'label'), [
                'Projects' => array_column($trend, 'projects'),
                'Issues' => array_column($trend, 'issues'),
            ]),
            'priorities' => $metrics->issuePriorities(),
        ]);
    }

    private function activeDays(): int
    {
        return in_array((int) $this->days, DashboardMetrics::TIMEFRAMES, true)
            ? (int) $this->days
            : DashboardMetrics::DEFAULT_TIMEFRAME;
    }
}
