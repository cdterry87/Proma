<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create my user
        User::factory()->create([
            'name' => 'Chase Terry',
            'email' => 'chase@chaseterry.com',
            'title' => 'Developer',
            'password' => Hash::make('password1'),
            'demo' => false
        ]);

        // Create demo user
        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'title' => 'Demo',
            'password' => Hash::make('password1'),
            'demo' => true
        ]);

        // Run the demo user seeder
        $this->call(DemoUserSeeder::class);
    }
}
