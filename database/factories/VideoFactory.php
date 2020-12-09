<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Video::class, function (Faker $faker) {
    $rating = \App\Models\Video::RATING_LIST[array_rand(\App\Models\Video::RATING_LIST)];
    return [
        'title' => $faker->sentence(3),
        'description' => $faker->sentence(10),
        'year_launched' => rand(1895, 2022),
        'opened' => rand(0,1),
        'rating' => $rating,
        'duration' => rand(1,30)
    ];
});
