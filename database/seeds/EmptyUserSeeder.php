<?php

use Illuminate\Database\Seeder;

use App\User;

class EmptyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        factory(User::class)->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Creating random users
        factory(User::class, 24)->create();
    }
}
