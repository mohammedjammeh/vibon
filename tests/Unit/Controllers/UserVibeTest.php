<?php

namespace Tests\Unit\Controllers;

use App\Events\UserLeftVibe;
use App\Events\UserRemovedFromVibe;
use App\Notifications\JoinedVibe;
use App\Notifications\LeftVibe;
use App\Notifications\RemovedFromAVibe;
use App\Vibe;
use App\User;
use App\Events\UserJoinedVibe;
use App\Listeners\SendUserJoinedVibeNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserVibeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_that_the_user_joined_vibe_event_is_called_when_user_joins_vibe()
    {
        Event::fake();

        $vibe = factory(Vibe::class)->create();
        $owner = factory(User::class)->create();

        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->post(route('user-vibe.join', $vibe->id));

        Event::assertDispatched(UserJoinedVibe::class);
    }

    public function test_that_the_vibe_owner_is_notified_when_user_joins_vibe()
    {
        Notification::fake();

        $vibe = factory(Vibe::class)->create();
        $owner = factory(User::class)->create();

        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->post(route('user-vibe.join', $vibe->id));

        Notification::assertSentTo(
            [$owner], JoinedVibe::class
        );
    }

    public function test_that_the_user_left_vibe_event_is_called_when_user_leaves_vibe()
    {
        Event::fake();

        $vibe = factory(Vibe::class)->create();

        $vibe->users()->attach($this->user->id, ['owner' => false]);

        $this->delete(route('user-vibe.leave', $vibe));

        Event::assertDispatched(UserLeftVibe::class);
    }

    public function test_that_the_vibe_owner_is_notified_when_user_leaves_vibe()
    {
        Notification::fake();

        $vibe = factory(Vibe::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => false]);
        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->delete(route('user-vibe.leave', $vibe));

        Notification::assertSentTo(
            [$owner], LeftVibe::class
        );
    }

    public function test_that_the_user_removed_from_vibe_event_is_called_when_user_is_removed_from_a_vibe()
    {
        Event::fake();

        $vibe = factory(Vibe::class)->create();
        $user = factory(User::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($user->id, ['owner' => false]);
        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->actingAs($owner);
         $this->delete(route('user-vibe.remove', [
            'vibe' => $vibe->id,
            'user' => $user->id
        ]));

        Event::assertDispatched(UserRemovedFromVibe::class);
    }

    public function test_that_a_user_who_is_not_an_owner_of_a_vibe_can_remove_a_user_from_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $user = factory(User::class)->create();
        $nonOwner = factory(User::class)->create();
        $vibe->users()->attach($user->id, ['owner' => false]);
        $vibe->users()->attach($nonOwner->id, ['owner' => false]);

        $this->actingAs($nonOwner);
        $this->delete(route('user-vibe.remove', [
            'vibe' => $vibe->id,
            'user' => $user->id
        ]));

        $this->assertDatabaseHas('user_vibe', [
            'vibe_id' => $vibe->id,
            'user_id' => $user->id,
            'owner' => false
        ]);
    }

    public function test_that_the_user_is_notified_when_he_is_removed_from_a_vibe()
    {
        Notification::fake();

        $vibe = factory(Vibe::class)->create();
        $user = factory(User::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($user->id, ['owner' => false]);
        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->actingAs($owner);
        $this->delete(route('user-vibe.remove', [
            'vibe' => $vibe->id,
            'user' => $user->id
        ]));

        Notification::assertSentTo(
            [$user], RemovedFromAVibe::class
        );
    }
}
