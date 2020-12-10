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

    public function test_the_notifications_for_method_returns_user_notifications_for_a_specific_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->post(route('user-vibe.join', $vibe));

        $this->assertEquals($owner->notificationsFor($vibe)->first()->id, $owner->notifications->first()->id);
    }
}
