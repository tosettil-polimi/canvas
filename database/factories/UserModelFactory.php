<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(config('canvas.user'), function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt($faker->password),
    ];
});
