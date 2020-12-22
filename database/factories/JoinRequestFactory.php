<?php

use App\User;
use App\Vibe;
use App\JoinRequest;
use App\Notifications\RequestToJoinAVibe;
use Faker\Generator as Faker;

$factory->define(JoinRequest::class, function (Faker $faker) {
    return [
		'vibe_id' => factory(Vibe::class)->create(),
		'user_id' => factory(User::class)->create(),
    ];
});