<?php

namespace Tests\Unit\Controllers;

use App\Events\JoinRequestAccepted;
use App\Events\JoinRequestCancelled;
use App\Events\JoinRequestRejected;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Vibe;
use App\User;
use App\JoinRequest;
use App\Events\JoinRequestSent;

class JoinRequestTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_user_can_send_a_request_to_join_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $this->post(route('join-request.store', $vibe));
        $this->assertDatabaseHas('join_requests', [
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_sending_a_join_request_will_trigger_the_join_request_event()
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

    public function test_vibe_owner_gets_the_notification_from_join_request()
    {
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);

        $this->post(route('join-request.store', $vibe));

        $vibeOwnerNotification = $vibe->owner->notifications->first();
        $this->assertDatabaseHas('notifications', [
            'type' => $vibeOwnerNotification->type,
            'notifiable_id' => $vibeOwnerNotification->notifiable_id
        ]);
        $this->assertEquals($vibeOwnerNotification->data['vibe_id'], $vibe->id);
        $this->assertEquals($vibeOwnerNotification->data['user_id'], $this->user->id);
    }

    public function test_user_can_cancel_the_request_to_join_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);
        event(new JoinRequestSent($joinRequest));

        $this->delete(route('join-request.destroy', [
            'joinRequest' => $joinRequest
        ]));
        $this->assertDatabaseMissing('join_requests', [
            'vibe_id' => $joinRequest->vibe_id,
            'user_id' => $joinRequest->user_id
        ]);
    }

    public function test_cancelling_a_join_request_will_trigger_the_join_request_cancel_event()
    {
        Event::fake();

        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);
        event(new JoinRequestSent($joinRequest));

        $this->delete(route('join-request.destroy', [
            'joinRequest' => $joinRequest
        ]));

        Event::assertDispatched(JoinRequestCancelled::class);
    }

    public function test_vibe_owner_can_accept_a_join_request()
    {
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);

        $this->actingAs($vibeOwner);
        $this->delete(route('join-request.accept', [
            'joinRequest' => $joinRequest
        ]), ['accept' => true]);

        $this->assertDatabaseHas('user_vibe', [
            'user_id' => $joinRequest->user->id,
            'vibe_id' => $vibe->id,
            'owner' => false
        ]);
    }

    public function test_accepting_a_join_request_triggers_the_join_request_accepted_event()
    {
        Event::fake();

        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);

        $this->actingAs($vibeOwner);
        $this->delete(route('join-request.accept', [
            'joinRequest' => $joinRequest
        ]), ['accept' => true]);

        Event::assertDispatched(JoinRequestAccepted::class);
    }

    public function test_vibe_owner_can_reject_a_join_request()
    {
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);

        $this->actingAs($vibeOwner);
        $this->delete(route('join-request.reject', [
            'joinRequest' => $joinRequest
        ]));

        $this->assertDatabaseMissing('user_vibe', [
            'user_id' => $joinRequest->user->id,
            'vibe_id' => $vibe->id,
        ]);
    }

    public function test_rejecting_a_join_request_triggers_the_join_request_rejected_event()
    {
        Event::fake();

        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);

        $this->actingAs($vibeOwner);
        $this->delete(route('join-request.reject', [
            'joinRequest' => $joinRequest
        ]));

        Event::assertDispatched(JoinRequestRejected::class);
    }

    public function test_join_request_user_gets_notified_after_being_accepted()
    {
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);
        event(new JoinRequestSent($joinRequest));

        $this->actingAs($vibeOwner);
        $this->delete(route('join-request.accept', [
            'joinRequest' => $joinRequest
        ]), ['accept' => true]);

        $joinRequesterResponseNotification = $joinRequest->user->notifications->first();
        $this->assertDatabaseHas('notifications', [
            'type' => $joinRequesterResponseNotification->type,
            'notifiable_id' => $joinRequest->user->id
        ]);
        $this->assertEquals($joinRequesterResponseNotification->data['vibe_id'], $vibe->id);
    }

    public function test_join_request_user_gets_notified_after_being_rejected()
    {
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);
        event(new JoinRequestSent($joinRequest));

        $this->actingAs($vibeOwner);
        $this->delete(route('join-request.reject', [
            'joinRequest' => $joinRequest
        ]));

        $joinRequesterResponseNotification = $joinRequest->user->notifications->first();
        $this->assertDatabaseHas('notifications', [
            'type' => $joinRequesterResponseNotification->type,
            'notifiable_id' => $joinRequest->user->id
        ]);
        $this->assertEquals($joinRequesterResponseNotification->data['vibe_id'], $vibe->id);
    }
}
