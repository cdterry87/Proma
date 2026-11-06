<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
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
            'name' => fake()->catchPhrase(),
            'description' => fake()->paragraph(),
            'start_date' => today()->subDays(fake()->numberBetween(1, 60)),
            'due_date' => today()->addDays(fake()->numberBetween(30, 90)),
            'completed_date' => null,
        ];
    }

    public function forClient(Client $client): static
    {
        return $this->state([
            'user_id' => $client->user_id,
            'client_id' => $client->id,
        ]);
    }

    public function completed(): static
    {
        return $this->state(['completed_date' => today()]);
    }
}
