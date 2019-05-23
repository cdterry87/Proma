<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Project::class, function (Faker $faker) {
    return [
        'name' => ucwords($faker->sentence(rand(3, 6), true)),
        'description' => $faker->paragraph(5),
        'team_id' => rand(1, 9),
        'client_id' => rand(1, 9)
    ];
});
