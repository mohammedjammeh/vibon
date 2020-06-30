<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Vibe;
use App\User;
use App\Notifications\RemovedFromAVibe;

class UserVibeTest extends TestCase
{
	use WithFaker, RefreshDatabase;

	public function test_user_can_join_a_vibe()
	{
		$vibe = factory(Vibe::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($owner->id, ['owner' => true]);

		$response = $this->post(route('user-vibe.join', $vibe->id));
        $responseData = $response->original;
		$expectedMessage = 'Welcome to ' .  $responseData['vibe']->name . '.';

        $this->assertEquals($expectedMessage, $responseData['message']);
        $this->assertEquals($vibe->id, $responseData['vibe']->id);
		$this->assertDatabaseHas('user_vibe', [
			'user_id' => $this->user->id,
			'vibe_id' => $vibe->id,
			'owner' => false
		]);
	}

    public function test_user_can_leave_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($owner->id, ['owner' => true]);
        $vibe->users()->attach($this->user->id, ['owner' => false]);

        $response = $this->delete(route('user-vibe.leave', $vibe));
        $responseData = $response->original;
        $expectedMessage = 'You are no longer part of ' .  $responseData['vibe']->name . '.';

        $this->assertEquals($expectedMessage, $responseData['message']);
        $this->assertEquals($vibe->id, $responseData['vibe']->id);
        $this->assertDatabaseMissing('user_vibe', [
            'user_id' => $this->user->id,
            'vibe_id' => $vibe->id,
            'owner' => false
        ]);
    }

	public function test_user_can_be_removed_from_a_vibe()
	{
		$vibe = factory(Vibe::class)->create();
		$user = factory(User::class)->create();
    	$vibe->users()->attach($user->id, ['owner' => false]);

		$response = $this->delete(route('user-vibe.remove', [
			'vibe' => $vibe->id,
			'user' => $user->id
		]));
        $responseData = $response->original;
        $expectedMessage = $user->username . ' is no longer a member of ' .  $responseData['vibe']->name . '.';

        $this->assertEquals($expectedMessage, $responseData['message']);
        $this->assertEquals($vibe->id, $responseData['vibe']->id);
		$this->assertDatabaseMissing('user_vibe', [
			'user_id' => $user->id,
			'vibe_id' => $vibe->id,
			'owner' => false
		]);

        $this->assertDatabaseHas('notifications', [
            'type' => $user->notifications->first()->type,
            'notifiable_type' => $user->notifications->first()->notifiable_type,
            'notifiable_id' => $user->notifications->first()->notifiable_id
        ]);
	}
}
