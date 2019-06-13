<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\IssueActivity::class, function (Faker $faker) {
    return [
        'issue_id' => rand(1, 9),
        'description' => $faker->paragraph(10),
    ];
});
