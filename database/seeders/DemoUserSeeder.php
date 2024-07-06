<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Issue;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get faker instance
        $faker = \Faker\Factory::create();

        // Get the demo user
        $demo = User::where('demo', true)->orderBy('created_at', 'asc')->first();

        // Set random counts
        $clientCount = rand(10, 20);
        $projectCount = rand(5, 15);
        $issueCount = rand(1, 5);

        if ($demo) {
            // Create clients for user
            $clients = Client::factory()->count($clientCount)->create([
                'user_id' => $demo->id
            ]);

            // Create projects for user
            foreach ($clients as $client) {
                $projects = Project::factory()->count($projectCount)->create([
                    'user_id' => $demo->id,
                    'client_id' => $client->id,
                ]);

                // Create issues for user
                foreach ($projects as $project) {
                    Issue::factory()->count($issueCount)->create([
                        'client_id' => $client->id,
                        'user_id' => $demo->id,
                        'project_id' => $project->id,
                    ]);
                }
            }
        }
    }
}
