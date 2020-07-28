<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Canvas\Models\Topic::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->uuid,
        'slug' => $faker->slug,
        'name' => $faker->word,
        'user_id' => function () {
            return factory(config('canvas.user'))->create()->id;
        },
    ];
});
