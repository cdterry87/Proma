<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Issue::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'client_id' => rand(1, 9),
        'project_id' => rand(1, 9),
        'description' => $faker->paragraph(10),
        'priority' => rand(1, 5),
    ];
});
