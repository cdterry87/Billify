<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Bill::class, function (Faker $faker) {
    return [
        'name' => ucwords($faker->sentence(rand(2, 4), true)),
        'description' => $faker->paragraph(10),
        'amount' => rand(100, 1000),
        'day' => rand(1, 30)
    ];
});
