<?php

namespace App\Livewire;

use App\Models\Issue;
use App\Models\Project;
use Livewire\Component;

class Home extends Component
{
    public $days = 30;

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
        ]);
    }
}
