<?php

use App\Track;
use App\Genre;
use Faker\Generator as Faker;

$factory->define(Track::class, function (Faker $faker) {
    return [
    	'api_id' => $faker->uuid,
    ];
});

$factory->afterCreating(Track::class, function ($track, $faker) {
	$genres = factory(Genre::class, 2)->create();
    $genresIDs = $genres->pluck('id')->toArray();
	$track->genres()->attach($genresIDs);
});
