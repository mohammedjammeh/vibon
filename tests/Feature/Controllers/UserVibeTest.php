<?php

namespace Tests\Feature\Controller;

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
		$this->post(route('user-vibe.store', $vibe->id));
		$this->assertDatabaseHas('user_vibe', [
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
		$this->delete(route('user-vibe.destroy', [
			'vibe' => $vibe->id, 
			'user' => $user->id
		]));
		$this->assertDatabaseMissing('user_vibe', [
			'user_id' => $user->id,
			'vibe_id' => $vibe->id,
			'owner' => false
		]);
	}

	public function test_user_who_has_been_removed_by_vibe_owner_gets_a_notification()
	{
		$vibe = factory(Vibe::class)->create();
		$user = factory(User::class)->create();
    	$vibe->users()->attach($user->id, ['owner' => false]);
		$attributes = ['vibe-member-remove' => true];
		$this->delete(route('user-vibe.destroy', [
			'vibe' => $vibe->id, 
			'user' => $user->id
		]), $attributes);
		$this->assertDatabaseHas('notifications', [
			'type' => $user->notifications->first()->type,
			'notifiable_type' => $user->notifications->first()->notifiable_type,
			'notifiable_id' => $user->notifications->first()->notifiable_id
		]);
	}
}
