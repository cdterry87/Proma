<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserNotification>
 */
class UserNotificationFactory extends Factory
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
            'title' => fake()->sentence(3),
            'message' => fake()->sentence(),
            'read_at' => null,
        ];
    }

    public function read(): static
    {
        return $this->state(['read_at' => now()]);
    }
}
