<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'taskable_type' => 'project',
            'taskable_id' => Project::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'start_date' => null,
            'due_date' => null,
            'completed_date' => null,
        ];
    }

    public function forProject(Project $project): static
    {
        return $this->state([
            'taskable_type' => $project->getMorphClass(),
            'taskable_id' => $project->id,
        ]);
    }

    public function forIssue(Issue $issue): static
    {
        return $this->state([
            'taskable_type' => $issue->getMorphClass(),
            'taskable_id' => $issue->id,
        ]);
    }

    public function completed(): static
    {
        return $this->state(['completed_date' => today()]);
    }
}
