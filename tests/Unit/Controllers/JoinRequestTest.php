<?php

namespace Tests\Unit\Controller;

use App\Vibe;
use App\User;
use App\JoinRequest;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Events\JoinRequestSent;

class JoinRequestTest extends TestCase
{
	use WithFaker, RefreshDatabase;

	public function test_user_can_send_a_request_to_join_a_vibe_and_the_join_request_sent_event_will_be_triggered()
	{
		Event::fake();
		$vibe = factory(Vibe::class)->create();
		$this->post(route('join-request.store', $vibe));
		$this->assertDatabaseHas('join_requests', [
			'vibe_id' => $vibe->id,
			'user_id' => $this->user->id
		]);
		Event::assertDispatched(JoinRequestSent::class);
	}

	public function test_vibe_owner_will_receive_notification_when_user_sends_request_to_join_his_vibe()
	{
		$vibe = factory(Vibe::class)->create();
		$vibeOwner = factory(User::class)->create();
		$vibe->users()->attach($vibeOwner->id, ['owner' => true]);
		$this->post(route('join-request.store', $vibe));

		event(new JoinRequestSent(JoinRequest::first()));
		$vibeOwnerNotification = $vibe->owner->notifications->first();
		$this->assertDatabaseHas('notifications', [
			'type' => $vibeOwnerNotification->type,
			'notifiable_id' => $vibeOwnerNotification->notifiable_id
		]);
		$this->assertEquals($vibeOwnerNotification->data['vibe_id'], $vibe->id);
		$this->assertEquals($vibeOwnerNotification->data['requester_id'], $this->user->id);

	}
}
