<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Issue;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduledJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_issues_notifications_job()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a resolved issue for yesterday
        $resolvedIssue = Issue::factory()->create(['user_id' => $user->id, 'resolved_date' => now()->subDay()]);

        // Create a priority 4 issue for 3 days ago
        $criticalIssue = Issue::factory()->create(['user_id' => $user->id, 'priority' => 4, 'created_at' => now()->subDays(4)]);

        // Create a priority 3 issue for 7 days ago
        $highIssue = Issue::factory()->create(['user_id' => $user->id, 'priority' => 3, 'created_at' => now()->subDays(8)]);

        // Create a priority 2 issue for 14 days ago
        $mediumIssue = Issue::factory()->create(['user_id' => $user->id, 'priority' => 2, 'created_at' => now()->subDays(15)]);

        // Create a priority 1 issue for 30 days ago
        $lowIssue = Issue::factory()->create(['user_id' => $user->id, 'priority' => 1, 'created_at' => now()->subDays(31)]);

        // Create new issues of each priority
        $newCriticalIssue = Issue::factory()->create(['user_id' => $user->id, 'priority' => 4, 'created_at' => now()]);
        $newHighIssue = Issue::factory()->create(['user_id' => $user->id, 'priority' => 3, 'created_at' => now()]);
        $newMediumIssue = Issue::factory()->create(['user_id' => $user->id, 'priority' => 2, 'created_at' => now()]);
        $newLowIssue = Issue::factory()->create(['user_id' => $user->id, 'priority' => 1, 'created_at' => now()]);

        // Run the job
        $job = new \App\Jobs\IssuesNotificationsJob();
        $job->handle();

        // Assert notifications were sent
        $this->assertDatabaseHas('issues_notifications_sent', [
            'user_id' => $user->id,
            'issue_id' => $resolvedIssue->id,
            'priority' => $resolvedIssue->priority,
            'resolved' => true,
        ]);
        $this->assertDatabaseHas('issues_notifications_sent', [
            'user_id' => $user->id,
            'issue_id' => $criticalIssue->id,
            'priority' => $criticalIssue->priority,
            'resolved' => false,
        ]);
        $this->assertDatabaseHas('issues_notifications_sent', [
            'user_id' => $user->id,
            'issue_id' => $highIssue->id,
            'priority' => $highIssue->priority,
            'resolved' => false,
        ]);
        $this->assertDatabaseHas('issues_notifications_sent', [
            'user_id' => $user->id,
            'issue_id' => $mediumIssue->id,
            'priority' => $mediumIssue->priority,
            'resolved' => false,
        ]);
        $this->assertDatabaseHas('issues_notifications_sent', [
            'user_id' => $user->id,
            'issue_id' => $lowIssue->id,
            'priority' => $lowIssue->priority,
            'resolved' => false,
        ]);

        // Assert notifications were NOT sent for new issues
        $this->assertDatabaseMissing('issues_notifications_sent', [
            'user_id' => $user->id,
            'issue_id' => $newCriticalIssue->id,
            'priority' => $newCriticalIssue->priority,
            'resolved' => false,
        ]);
        $this->assertDatabaseMissing('issues_notifications_sent', [
            'user_id' => $user->id,
            'issue_id' => $newHighIssue->id,
            'priority' => $newHighIssue->priority,
            'resolved' => false,
        ]);
        $this->assertDatabaseMissing('issues_notifications_sent', [
            'user_id' => $user->id,
            'issue_id' => $newMediumIssue->id,
            'priority' => $newMediumIssue->priority,
            'resolved' => false,
        ]);
        $this->assertDatabaseMissing('issues_notifications_sent', [
            'user_id' => $user->id,
            'issue_id' => $newLowIssue->id,
            'priority' => $newLowIssue->priority,
            'resolved' => false,
        ]);
    }

    public function test_projects_notifications_job()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a project with a due_date in 10 days
        $dueProject = Project::factory()->create(['user_id' => $user->id, 'due_date' => now()->addDays(10)]);

        // Create a project with a due_date of 2 days ago
        $overdueProject = Project::factory()->create(['user_id' => $user->id, 'due_date' => now()->subDays(2)]);

        // Create a project that was completed 2 days ago
        $completedProject = Project::factory()->create(['user_id' => $user->id, 'completed_date' => now()->subDays(2)]);

        // Create a project that is due in 30 days
        $notDueProject = Project::factory()->create(['user_id' => $user->id, 'due_date' => now()->addDays(30)]);

        // Run the job
        $job = new \App\Jobs\ProjectsNotificationsJob();
        $job->handle();

        // Assert notifications were sent for due, overdue, and completled projects
        $this->assertDatabaseHas('projects_notifications_sent', [
            'user_id' => $user->id,
            'project_id' => $dueProject->id,
            'due' => true,
            'overdue' => false,
            'completed' => false
        ]);
        $this->assertDatabaseHas('projects_notifications_sent', [
            'user_id' => $user->id,
            'project_id' => $overdueProject->id,
            'due' => false,
            'overdue' => true,
            'completed' => false
        ]);
        $this->assertDatabaseHas('projects_notifications_sent', [
            'user_id' => $user->id,
            'project_id' => $completedProject->id,
            'due' => false,
            'overdue' => false,
            'completed' => true
        ]);

        // Assert notifications were not sent for projects that are not due
        $this->assertDatabaseMissing('projects_notifications_sent', [
            'user_id' => $user->id,
            'project_id' => $notDueProject->id,
        ]);
    }

    public function test_projects_tasks_notifications_job()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a project
        $project = Project::factory()->create(['user_id' => $user->id]);

        // Create a task with a due_date in 10 days
        $dueTask = ProjectTask::factory()->create(['project_id' => $project->id, 'due_date' => now()->addDays(10)]);

        // Create a task with a due_date of 2 days ago
        $overdueTask = ProjectTask::factory()->create(['project_id' => $project->id, 'due_date' => now()->subDays(2)]);

        // Create a task that was completed 2 days ago
        $completedTask = ProjectTask::factory()->create(['project_id' => $project->id, 'completed_date' => now()->subDays(2)]);

        // Create a task that is due in 30 days
        $notDueTask = ProjectTask::factory()->create(['project_id' => $project->id, 'due_date' => now()->addDays(30)]);

        // Run the job
        $job = new \App\Jobs\ProjectsTasksNotificationsJob();
        $job->handle();

        // Assert notifications were sent for due, overdue, and completled projects
        $this->assertDatabaseHas('projects_tasks_notifications_sent', [
            'user_id' => $user->id,
            'project_task_id' => $dueTask->id,
            'due' => true,
            'overdue' => false,
            'completed' => false
        ]);
        $this->assertDatabaseHas('projects_tasks_notifications_sent', [
            'user_id' => $user->id,
            'project_task_id' => $overdueTask->id,
            'due' => false,
            'overdue' => true,
            'completed' => false
        ]);
        $this->assertDatabaseHas('projects_tasks_notifications_sent', [
            'user_id' => $user->id,
            'project_task_id' => $completedTask->id,
            'due' => false,
            'overdue' => false,
            'completed' => true
        ]);

        // Assert notifications were not sent for projects that are not due
        $this->assertDatabaseMissing('projects_tasks_notifications_sent', [
            'user_id' => $user->id,
            'project_task_id' => $notDueTask->id,
        ]);
    }

    public function test_routine_cleanup_job()
    {
        // Create a demo user with client, project, and issue
        $demoUser = User::factory()->create(['demo' => true]);
        $client = Client::factory()->create(['user_id' => $demoUser->id]);
        $project = Project::factory()->create(['user_id' => $demoUser->id, 'client_id' => $client->id]);
        $issue = Issue::factory()->create(['user_id' => $demoUser->id, 'project_id' => $project->id, 'client_id' => $client->id]);

        // Create another non-demo user with client, project, and issue
        $nonDemoUser = User::factory()->create(['demo' => false]);
        $client2 = Client::factory()->create(['user_id' => $nonDemoUser->id]);
        $project2 = Project::factory()->create(['user_id' => $nonDemoUser->id, 'client_id' => $client2->id]);
        $issue2 = Issue::factory()->create(['user_id' => $nonDemoUser->id, 'project_id' => $project2->id, 'client_id' => $client2->id]);

        // Assert database has client, project, and issue for demo user
        $this->assertDatabaseHas('clients', ['id' => $client->id]);
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
        $this->assertDatabaseHas('issues', ['id' => $issue->id]);

        // Assert database has client, project, and issue for non-demo user
        $this->assertDatabaseHas('clients', ['id' => $client2->id]);
        $this->assertDatabaseHas('projects', ['id' => $project2->id]);
        $this->assertDatabaseHas('issues', ['id' => $issue2->id]);

        // Run the job
        $job = new \App\Jobs\RoutineCleanupJob();
        $job->handle();

        // Assert database does not have client, project, and issue
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('issues', ['id' => $issue->id]);

        // Assert database still has client, project, and issue for non-demo user
        $this->assertDatabaseHas('clients', ['id' => $client2->id]);
        $this->assertDatabaseHas('projects', ['id' => $project2->id]);
        $this->assertDatabaseHas('issues', ['id' => $issue2->id]);
    }
}
