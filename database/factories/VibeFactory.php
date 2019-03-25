<?php

use App\Vibe;
use App\User;
use App\Track;
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
    $users = factory(User::class, 2)->create();
	$vibe->users()->attach($users->first()->id, ['owner' => true]);
    $vibe->users()->attach($users->last()->id, ['owner' => false]);

	$tracks = factory(Track::class, 2)->create();
    $tracksIDs = $tracks->pluck('id')->toArray();
	$vibe->tracks()->attach($tracksIDs, ['auto_related' => false]);
});
