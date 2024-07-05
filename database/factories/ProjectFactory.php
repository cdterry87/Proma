<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
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
        $project_date = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'start_date' => $project_date,
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'completed_date' => null,
            'created_at' => $project_date,
            'updated_at' => $project_date
        ];
    }
}
