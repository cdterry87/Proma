<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Client::class, function (Faker $faker) {
    return [
        'name' => ucwords($faker->sentence(rand(3, 6), true)),
        'description' => $faker->paragraph(5),
    ];
});
