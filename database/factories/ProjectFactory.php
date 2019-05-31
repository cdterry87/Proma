<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Project::class, function (Faker $faker) {

    $complete = rand(0, 1);
    $completed_date = null;
    if ($complete) {
        $completed_date = date('Y-m-d H:i:s');
    }

    return [
        'name' => ucwords($faker->sentence(rand(3, 6), true)),
        'description' => $faker->paragraph(10),
        'complete' => $complete,
        'completed_date' => $completed_date,
        'team_id' => rand(1, 9),
        'client_id' => rand(1, 9)
    ];
});
