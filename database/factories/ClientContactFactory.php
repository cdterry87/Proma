<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\ClientContact::class, function (Faker $faker) {
    return [
        'client_id' => rand(1, 9),
        'name' => $faker->name,
        'title' => ucwords($faker->sentence(rand(3, 6), true)),
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->tollFreePhoneNumber(),
    ];
});
