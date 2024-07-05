<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Issue>
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
        $issue_date = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'priority' => $this->faker->randomElement(['1', '2', '3', '4']),
            'resolved_date' => null,
            'client_id' => Client::factory(),
            'project_id' => Project::factory(),
            'created_at' => $issue_date,
            'updated_at' => $issue_date
        ];
    }
}
