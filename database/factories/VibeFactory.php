<?php

use App\Vibe;
use App\User;
use Faker\Generator as Faker;

$factory->define(Vibe::class, function (Faker $faker) {
    return [
    	'api_id' => $faker->uuid,
    	'description' => $faker->sentence,
    	'open' => $faker->boolean,
    	'auto_dj' => $faker->boolean
    ];
});

$factory->afterCreating(Vibe::class, function ($vibe, $faker) {
    $user = factory(User::class)->create();
	$vibe->users()->attach($user->id, ['owner' => 1]);
});
