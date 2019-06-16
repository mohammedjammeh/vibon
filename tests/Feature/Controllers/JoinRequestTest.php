<?php

namespace Tests\Feature\Controllers;

use App\Vibe;
use App\User;
use App\JoinRequest;
use Tests\TestCase;
use App\Events\JoinRequestSent;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JoinRequestTest extends TestCase
{
	use WithFaker, RefreshDatabase;

	public function test_user_can_send_a_request_to_join_a_vibe_and_vibe_owner_will_have_the_notification()
	{
		$vibe = factory(Vibe::class)->create();
		$vibeOwner = factory(User::class)->create();
		$vibe->users()->attach($vibeOwner->id, ['owner' => true]);

		$this->post(route('join-request.store', $vibe));
		$this->assertDatabaseHas('join_requests', [
			'vibe_id' => $vibe->id,
			'user_id' => $this->user->id
		]);
		$vibeOwnerNotification = $vibe->owner->notifications->first();
		$this->assertDatabaseHas('notifications', [
			'type' => $vibeOwnerNotification->type,
			'notifiable_id' => $vibeOwnerNotification->notifiable_id
		]);
		$this->assertEquals($vibeOwnerNotification->data['vibe_id'], $vibe->id);
		$this->assertEquals($vibeOwnerNotification->data['requester_id'], $this->user->id);
	}

	public function test_user_can_cancel_the_request_to_join_a_vibe_and_vibe_owner_will_no_longer_have_the_notification()
	{
		$vibe = factory(Vibe::class)->create();
		$vibeOwner = factory(User::class)->create();
		$vibe->users()->attach($vibeOwner->id, ['owner' => true]);
		$joinRequest = factory(JoinRequest::class)->create([
			'vibe_id' => $vibe->id,
			'user_id' => $this->user->id
		]);
		
		$this->delete(route('join-request.destroy', [
			'joinRequest' => $joinRequest 
		]));
		$this->assertDatabaseMissing('join_requests', [
			'vibe_id' => $joinRequest->vibe_id,
			'user_id' => $joinRequest->user_id
		]);
		$this->assertEmpty($vibe->owner->notifications->first());
	}

	public function test_vibe_owner_can_respond_to_a_join_request_from_a_user() 
	{
		$vibe = factory(Vibe::class)->create();
		$vibeOwner = factory(User::class)->create();
		$vibe->users()->attach($vibeOwner->id, ['owner' => true]);
		$joinRequest = factory(JoinRequest::class)->create([
			'vibe_id' => $vibe->id,
			'user_id' => $this->user->id
		]);
		event(new JoinRequestSent($joinRequest));

		$response = collect([true, false])->random();
		$this->delete(route('join-request.respond', [
			'joinRequest' => $joinRequest, 
		]), ['reject' => $response]);

		$this->assertDatabaseMissing('join_requests', [ 
			'vibe_id' => $vibe->id,
			'user_id' => $joinRequest->user->id
		]);

		$vibeOwnerNotificationFromJoinRequestUser = $vibe->owner->notifications            
			->where('data.requester_id', $joinRequest->user->id)
            ->where('data.vibe_id', $vibe->id)
            ->last();
     	$this->assertNotNull($vibeOwnerNotificationFromJoinRequestUser->read_at);

		$joinRequesterResponseNotification = $joinRequest->user->notifications->first();
		$this->assertDatabaseHas('notifications', [
			'type' => $joinRequesterResponseNotification->type,
			'notifiable_id' => $joinRequest->user->id
		]);
		$this->assertEquals($joinRequesterResponseNotification->data['vibe_id'], $vibe->id);
		$this->assertEquals($joinRequesterResponseNotification->data['response'], !$response);
	}

	public function test_vibe_owner_can_reject_a_join_request_from_a_user_and_the_user_will_not_be_a_memeber_of_the_vibe() 
	{
		$joinRequest = factory(JoinRequest::class)->create();
		$vibe = Vibe::where('id', $joinRequest->vibe_id)->first(); 
		$this->delete(route('join-request.respond', [
			'joinRequest' => $joinRequest, 
			'vibe' => $vibe
		]), ['reject' => true]);
		$this->assertDatabaseMissing('user_vibe', [
			'user_id' => $joinRequest->user->id,
			'vibe_id' => $vibe->id,
			'owner' => false
		]);
	}

	public function test_vibe_owner_can_accept_a_join_request_from_a_user_and_the_user_will_be_a_memeber_of_the_vibe()
	{
		$joinRequest = factory(JoinRequest::class)->create();
		$vibe = Vibe::where('id', $joinRequest->vibe_id)->first(); 
		$this->delete(route('join-request.respond', [
			'joinRequest' => $joinRequest, 
			'vibe' => $vibe
		]), ['reject' => false]);
		$this->assertDatabaseHas('user_vibe', [
			'user_id' => $joinRequest->user->id,
			'vibe_id' => $vibe->id,
			'owner' => false
		]);
	}
}