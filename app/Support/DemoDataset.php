<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\IssuePriority;
use App\Models\Client;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * A realistic set of records for a demo account. Every date is relative to
 * today, so the dashboard and notifications always have something to show.
 */
final class DemoDataset
{
    /**
     * Name, description, active.
     *
     * @var list<array{string, string, bool}>
     */
    private const array CLIENTS = [
        ['Northwind Traders', 'Wholesale food distributor with regional warehouses.', true],
        ['Harbor Logistics', 'Freight and shipping company moving off legacy tools.', true],
        ['Summit Fitness', 'Gym chain with a growing online membership.', true],
        ['Beacon Financial', 'Independent advisory firm with strict compliance needs.', true],
        ['Larkspur Learning', 'Online course provider for professional certificates.', true],
        ['Copperline Coffee', 'Specialty roaster selling through cafes and subscriptions.', true],
        ['Riverbend Clinic', 'Family health clinic. On hold until next budget cycle.', false],
        ['Atlas Architects', 'Mid-sized architecture studio with three offices.', true],
    ];

    /**
     * Name, client, days since created, days until due, days since completed.
     *
     * @var list<array{string, int, int, int|null, int|null}>
     */
    private const array PROJECTS = [
        ['Website Redesign', 0, 120, -12, null],
        ['Customer Portal', 0, 60, 6, null],
        ['Brand Refresh', 1, 300, -200, 190],
        ['Inventory Sync', 1, 45, 25, null],
        ['Mobile App Launch', 2, 25, 8, null],
        ['Analytics Dashboard', 2, 200, -120, 110],
        ['Payment Migration', 3, 95, -3, null],
        ['Reporting Automation', 3, 12, 40, null],
        ['Onboarding Flow', 4, 150, -90, 85],
        ['Knowledge Base', 4, 35, 60, null],
        ['Marketing Site', 5, 20, 2, null],
        ['Data Warehouse Cleanup', 5, 340, -280, 275],
        ['Scheduling Tool', 6, 70, null, null],
        ['Security Audit', 7, 15, 9, null],
        ['Email Campaign Builder', 7, 50, -5, 8],
    ];

    /**
     * Name, priority, client, project, days since created, days since resolved.
     *
     * @var list<array{string, IssuePriority, int, int|null, int, int|null}>
     */
    private const array ISSUES = [
        ['Checkout page times out', IssuePriority::Critical, 0, 0, 6, null],
        ['Search results ignore filters', IssuePriority::Medium, 0, 0, 20, null],
        ['Password reset email delayed', IssuePriority::High, 0, 1, 10, null],
        ['Invoice totals round incorrectly', IssuePriority::High, 0, null, 140, 130],
        ['Logo blurry on high density screens', IssuePriority::Low, 1, 2, 290, 280],
        ['Stock counts lag behind orders', IssuePriority::High, 1, 3, 9, null],
        ['Duplicate records after import', IssuePriority::Medium, 1, 3, 28, 15],
        ['Tracking links return an error', IssuePriority::Critical, 1, null, 2, null],
        ['App crashes on older phones', IssuePriority::Critical, 2, 4, 4, null],
        ['Push notifications arrive twice', IssuePriority::Low, 2, 4, 18, null],
        ['Chart labels overlap on tablets', IssuePriority::Low, 2, 5, 180, 170],
        ['Class bookings fail near midnight', IssuePriority::High, 2, null, 55, 40],
        ['Failed payments are not retried', IssuePriority::Critical, 3, 6, 33, 30],
        ['Statements missing a footer', IssuePriority::Low, 3, 6, 40, null],
        ['Export drops the last row', IssuePriority::Medium, 3, 7, 5, null],
        ['Audit log has a gap in March', IssuePriority::High, 3, null, 240, 225],
        ['Progress resets after logout', IssuePriority::High, 4, 8, 130, 120],
        ['Certificate names cut off', IssuePriority::Medium, 4, 9, 22, 4],
        ['Videos buffer on slow connections', IssuePriority::Medium, 4, null, 16, null],
        ['Quiz scores show the wrong total', IssuePriority::High, 4, 9, 3, null],
        ['Subscription emails link to staging', IssuePriority::Critical, 5, 10, 1, null],
        ['Map shows closed cafes', IssuePriority::Low, 5, 10, 11, null],
        ['Nightly load misses new tables', IssuePriority::Medium, 5, 11, 320, 300],
        ['Gift cards reject valid codes', IssuePriority::High, 5, null, 75, 60],
        ['Calendar shows the wrong time zone', IssuePriority::Medium, 6, 12, 65, null],
        ['Reminder texts sent too early', IssuePriority::Low, 6, 12, 90, null],
        ['Old staff accounts still active', IssuePriority::Critical, 7, 13, 13, 6],
        ['File shares open to everyone', IssuePriority::High, 7, 13, 8, null],
        ['Unsubscribe link broken', IssuePriority::Medium, 7, 14, 48, 20],
        ['Plan viewer slow on large drawings', IssuePriority::Low, 7, null, 360, 345],
    ];

    /**
     * @var list<string>
     */
    private const array PROJECT_TASKS = ['Confirm requirements', 'Draft the plan', 'Build the first version', 'Review with the client'];

    /**
     * @var list<string>
     */
    private const array ISSUE_TASKS = ['Reproduce the problem', 'Ship the fix'];

    public function seed(User $user): void
    {
        $clients = array_map(fn (array $client): Client => $this->createClient($user, ...$client), self::CLIENTS);
        $projects = array_map(fn (array $project): Project => $this->createProject($user, $clients, ...$project), self::PROJECTS);

        foreach (self::ISSUES as $index => $issue) {
            $this->createIssue($user, $clients, $projects, $index, ...$issue);
        }
    }

    private function createClient(User $user, string $name, string $description, bool $active): Client
    {
        $client = $user->clients()->create(compact('name', 'description', 'active'));

        foreach (range(1, fake()->numberBetween(1, 3)) as $ignored) {
            $client->contacts()->create([
                'name' => fake()->name(),
                'title' => fake()->jobTitle(),
                'email' => fake()->safeEmail(),
                'phone' => fake()->numerify('555-###-####'),
            ]);
        }

        return $client;
    }

    /**
     * @param  list<Client>  $clients
     */
    private function createProject(User $user, array $clients, string $name, int $client, int $createdAgo, ?int $dueIn, ?int $completedAgo): Project
    {
        $project = $user->projects()->make([
            'client_id' => $clients[$client]->id,
            'name' => $name,
            'description' => "Planning and delivery of the {$name} work for {$clients[$client]->name}.",
            'start_date' => today()->subDays($createdAgo - 2),
            'due_date' => $dueIn === null ? null : today()->addDays($dueIn),
            'completed_date' => $completedAgo === null ? null : today()->subDays($completedAgo),
        ]);

        $this->saveAged($project, $createdAgo, $completedAgo);

        foreach (self::PROJECT_TASKS as $step => $title) {
            $done = $completedAgo !== null || $step === 0;
            $task = $project->tasks()->make([
                'title' => $title,
                'description' => "{$title} for {$name}.",
                'start_date' => today()->subDays($createdAgo - $step),
                // The last task is due with the project and the others lead up to it.
                'due_date' => $dueIn === null ? null : today()->addDays($dueIn - (count(self::PROJECT_TASKS) - 1 - $step) * 4),
                'completed_date' => $done ? today()->subDays($completedAgo ?? $createdAgo - 3) : null,
            ]);

            $this->saveAged($task, $createdAgo, $completedAgo);
        }

        return $project;
    }

    /**
     * @param  list<Client>  $clients
     * @param  list<Project>  $projects
     */
    private function createIssue(User $user, array $clients, array $projects, int $index, string $name, IssuePriority $priority, int $client, ?int $project, int $createdAgo, ?int $resolvedAgo): void
    {
        $issue = $user->issues()->make([
            'client_id' => $clients[$client]->id,
            'project_id' => $project === null ? null : $projects[$project]->id,
            'name' => $name,
            'description' => "Reported by {$clients[$client]->name}. Details are in the support thread.",
            'priority' => $priority,
            'resolved_date' => $resolvedAgo === null ? null : today()->subDays($resolvedAgo),
        ]);

        $this->saveAged($issue, $createdAgo, $resolvedAgo);

        if ($index % 3 === 0) {
            $this->createIssueTasks($issue, $createdAgo, $resolvedAgo);
        }
    }

    private function createIssueTasks(Issue $issue, int $createdAgo, ?int $resolvedAgo): void
    {
        foreach (self::ISSUE_TASKS as $title) {
            $task = $issue->tasks()->make([
                'title' => $title,
                'description' => "{$title}: {$issue->name}.",
                'completed_date' => $resolvedAgo === null ? null : today()->subDays($resolvedAgo),
            ]);

            $this->saveAged($task, $createdAgo, $resolvedAgo);
        }
    }

    /**
     * Timestamps drive the dashboard and the stale issue rule, so records are
     * backdated rather than all appearing to be new.
     */
    private function saveAged(Model $record, int $createdAgo, ?int $finishedAgo): void
    {
        $record->forceFill([
            'created_at' => now()->subDays($createdAgo),
            'updated_at' => now()->subDays($finishedAgo ?? $createdAgo),
        ])->save();
    }
}
