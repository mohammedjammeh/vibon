<?php

use App\User;
use App\Vibe;
use App\JoinRequest;
use App\Notifications\RequestToJoinAVibe;
use Faker\Generator as Faker;

$factory->define(JoinRequest::class, function (Faker $faker) {
	$vibe = factory(Vibe::class)->create();
	$user = factory(User::class)->create();
    return [
		'vibe_id' => $vibe->id,
		'user_id' => $user->id
    ];
});