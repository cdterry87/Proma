<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\TeamMember::class, function (Faker $faker) {
    return [
        'user_id' => rand(1, 9)
    ];
});
