<?php

use Faker\Generator as Faker;

$factory->define(App\Channel::class, function (Faker $faker) {
    $name = $faker->word;

    return [
        'name' => $faker->unique()->word,
        'slug' => $name,
        'description' => $faker->sentence,
    ];
});
