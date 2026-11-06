<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\IssuePriority;
use App\Models\Client;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Issue>
 */
class IssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'client_id' => fn (array $attributes) => Client::factory()->state(['user_id' => $attributes['user_id']]),
            'project_id' => null,
            'name' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(IssuePriority::cases()),
            'resolved_date' => null,
        ];
    }

    public function forClient(Client $client): static
    {
        return $this->state([
            'user_id' => $client->user_id,
            'client_id' => $client->id,
        ]);
    }

    public function forProject(Project $project): static
    {
        return $this->state([
            'user_id' => $project->user_id,
            'client_id' => $project->client_id,
            'project_id' => $project->id,
        ]);
    }

    public function resolved(): static
    {
        return $this->state(['resolved_date' => today()]);
    }
}
