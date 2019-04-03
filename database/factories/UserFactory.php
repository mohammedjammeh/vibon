<?php

use App\User;
use App\Track;
use App\Music\User as UserAPI;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'username' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'remember_token' => str_random(10),
        'api' => $faker->randomDigit,
        'access_token' => str_random(10),
        'refresh_token' => str_random(10),
        'token_set_at' => now()
    ];
});

$factory->afterCreating(User::class, function ($user, $faker) {
    $tracks = factory(Track::class, 2)->create();
    $tracksIDs = $tracks->pluck('id')->toArray();
	$user->tracks()->attach($tracksIDs, ['type' => UserAPI::TOP_TRACK]);
});
