<?php

use App\Vibe;
use Faker\Generator as Faker;

$factory->define(Vibe::class, function (Faker $faker) {
    return [
    	'api_id' => $faker->uuid,
    	'open' => $faker->boolean,
    	'auto_dj' => $faker->boolean
    ];
});
