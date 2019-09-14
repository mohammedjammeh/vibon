<?php

use Faker\Generator as Faker;

$factory->define(App\Vote::class, function (Faker $faker) {
    return [
        'user_id' => factory(\App\User::class)->create(),
        'vibe_id' => factory(\App\Vibe::class)->create(),
        'track_id' => factory(\App\Track::class)->create()
    ];
});
