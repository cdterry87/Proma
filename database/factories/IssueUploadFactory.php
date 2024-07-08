<?php

namespace Database\Factories;

use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IssueUpload>
 */
class IssueUploadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'issue_id' => Issue::factory(),
            'name' => $this->faker->word,
            'path' => $this->faker->word,
            'type' => $this->faker->word,
            'size' => $this->faker->randomNumber(),
        ];
    }
}
