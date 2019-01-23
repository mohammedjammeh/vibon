<?php

use App\Vibe;
use Faker\Generator as Faker;

$factory->define(Vibe::class, function (Faker $faker) {
    return [
    	'api_id' => $faker->uuid,
    	'title' => $faker->name,
    	'description' => $faker->text,
    	'type' => $faker->boolean,
    	'auto_dj' => $faker->boolean
    ];
});
