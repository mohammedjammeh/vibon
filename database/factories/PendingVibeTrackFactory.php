<?php

use App\Track;
use App\User;
use App\Vibe;
use Faker\Generator as Faker;

$factory->define(App\PendingVibeTrack::class, function (Faker $faker) {
    return [
        'track_id' => factory(Track::class)->create(),
        'user_id' => factory(User::class)->create(),
        'vibe_id' => factory(Vibe::class)->create(),
        'attach' => $faker->boolean,
    ];
});

$factory->state(App\PendingVibeTrack::class, 'attach', [
    'attach' => true,
]);

$factory->state(App\PendingVibeTrack::class, 'detach', [
    'attach' => false,
]);
