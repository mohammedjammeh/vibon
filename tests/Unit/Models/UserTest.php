<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Vibe;
use App\User;
use App\JoinRequest;
use App\Events\JoinRequestSent;
use App\Notifications\RequestToJoinAVibe;

class UserTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_request_notifications_method_gets_a_users_unread_notifications_based_on_the_join_requests_made_to_the_vibe_he_owns()
    {
        $this->markTestSkipped('Irrelevant');
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);
        event(new JoinRequestSent($joinRequest));

        $notification = $vibeOwner->requestNotifications()->first();
        $this->assertEquals($notification->notifiable_id, $vibeOwner->id);
        $this->assertEquals($notification->data['vibe_id'], $vibe->id);
        $this->assertEquals($notification->data['requester_id'], $this->user->id);
        $this->assertEquals($notification->read_at, null);
        $this->assertEquals($notification->type, RequestToJoinAVibe::class);
    }

    public function test_the_last_unread_request_notification_for_a_join_request_gets_the_last_unread_join_request_notification()
    {
        $this->markTestSkipped('Irrelevant');
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id
        ]);
        event(new JoinRequestSent($joinRequest));

        $notification = $vibeOwner->lastUnreadRequestNotificationFor($joinRequest);
        $this->assertEquals($notification->notifiable_id, $vibeOwner->id);
        $this->assertEquals($notification->data['vibe_id'], $vibe->id);
        $this->assertEquals($notification->data['requester_id'], $this->user->id);
        $this->assertEquals($notification->read_at, null);
        $this->assertEquals($notification->type, RequestToJoinAVibe::class);
    }

    public function test_the_is_authorised_with_method_checks_if_a_user_is_authorised_with_an_api_or_not()
    {
        $user = factory(User::class)->create(['api' => User::SPOTIFY]);
        $this->assertTrue($user->isAuthorisedWith(User::SPOTIFY));

        $anotherUser = factory(User::class)->create(['api' => 91]);
        $this->assertFalse($anotherUser->isAuthorisedWith(User::SPOTIFY));
    }

    public function test_the_scope_is_member_of_vibe_is_a_query_to_get_the_users_who_are_members_of_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $users = factory(User::class, 3)->create();
        $usersIDs = $users->pluck('id');
        $vibe->users()->attach($usersIDs, ['owner' => false]);

        $members = User::isMemberOf($vibe)->get();
        foreach ($members as $member) {
            $this->assertContains($member->id, $usersIDs);
        }
    }
}
