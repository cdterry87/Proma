<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Project::class, function (Faker $faker) {

    $completed = rand(0, 1);
    $completed_date = null;
    if ($completed) {
        $completed_date = date('Y-m-d H:i:s');
    }

    return [
        'user_id' => 1,
        'name' => ucwords($faker->sentence(rand(3, 6), true)),
        'description' => $faker->paragraph(10),
        'completed' => $completed,
        'completed_date' => $completed_date,
        'client_id' => rand(1, 9)
    ];
});
