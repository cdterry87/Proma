<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientContact>
 */
class ClientContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => fake()->name(),
            'title' => fake()->jobTitle(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('###-###-####'),
            'phone_ext' => null,
            'active' => true,
        ];
    }
}
