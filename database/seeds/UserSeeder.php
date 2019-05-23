<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Team;
use App\TeamMember;
use App\Client;
use App\ClientContact;
use App\Project;
use App\ProjectTask;

class UserSeeder extends Seeder
{
    public function run()
    {
        Team::unguard();
        Client::unguard();
        ClientContact::unguard();
        Project::unguard();
        ProjectTask::unguard();

        // Creating an admin user with associated teams, projects, and clients
        factory(User::class)->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ])->each(function ($admin) {
            $admin->teams()->saveMany(factory(Team::class, 9)->create()->each(function ($team) {
                $team->members()->save(factory(TeamMember::class)->create([
                    'team_id' => $team->first()->id,
                    'user_id' => 1
                ]));
            }));
            $admin->clients()->saveMany(factory(Client::class, 9)->create()->each(function ($client) {
                $client->contacts()->saveMany(factory(ClientContact::class, 9)->create([
                    'client_id' => $client->first()->id
                ]));
            }));
            $admin->projects()->saveMany(factory(Project::class, 9)->create()->each(function ($project) {
                $project->tasks()->saveMany(factory(ProjectTask::class, 9)->create([
                    'project_id' => $project->first()->id
                ]));
            }));
        });

        // Creating random users with associated teams, projects, and clients
        $users = factory(User::class, 9)->create()->each(function ($user) {
            $user->teams()->saveMany(factory(Team::class, 9)->make());
            $user->clients()->saveMany(factory(Client::class, 9)->make());
            $user->projects()->saveMany(factory(Project::class, 9)->make());
        });
    }
}
