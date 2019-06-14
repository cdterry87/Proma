<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Issue::class, function (Faker $faker) {

    $resolved = rand(0, 1);
    $resolved_date = null;
    if ($resolved) {
        $resolved_date = date('Y-m-d H:i:s');
    }

    return [
        'user_id' => 1,
        'client_id' => rand(1, 9),
        'project_id' => rand(1, 9),
        'description' => $faker->paragraph(10),
        'priority' => rand(1, 5),
        'resolved' => $resolved,
        'resolved_date' => $resolved_date,
    ];
});
