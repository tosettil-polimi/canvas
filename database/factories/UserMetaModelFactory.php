<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;

$factory->define(\Canvas\Models\UserMeta::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->randomNumber(),
        'user_id' => function () {
            return factory(config('canvas.user'))->create()->id;
        },
        'username' => Str::slug($faker->userName),
        'summary' => $faker->sentence,
        'avatar' => md5(trim(Str::lower($faker->email))),
        'dark_mode' => $faker->numberBetween(0, 1),
        'digest' => $faker->numberBetween(0, 1),
        'locale' => $faker->locale,
        'admin' => 0,
    ];
});
