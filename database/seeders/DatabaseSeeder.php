<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Permission;
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
            'guest' => false
        ]);

        // Create guest user
        User::factory()->create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'title' => 'Guest',
            'password' => Hash::make('password1'),
            'guest' => true
        ]);

        // Run the guest user seeder
        $this->call(GuestUserSeeder::class);
    }
}
