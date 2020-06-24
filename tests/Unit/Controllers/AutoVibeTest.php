<?php

namespace Tests\Unit\Controllers;

use App\Events\AutoVibeRefreshed;
use App\Track;
use App\User;
use App\Vibe;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use App\MusicAPI\User as UserAPI;

class AutoVibeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_that_the_auto_vibe_refreshed_event_is_triggered_when_a_vibe_is_refreshed()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $userTrack = factory(Track::class)->create();
        $user->tracks()->attach($userTrack->id, ['type' => UserAPI::TOP_TRACK]);

        $vibe = factory(Vibe::class)->create(['auto_dj' => true]);
        $vibe->users()->attach($user->id, ['owner' => true]);

        $vibeTrack = factory(Track::class)->create();
        $vibe->tracks()->attach($vibeTrack->id, ['auto_related' => true]);

        $this->actingAs($user);
        $this->post(route('auto-vibe.refresh', $vibe));

        Event::assertDispatched(AutoVibeRefreshed::class);
    }

    public function test_that_an_auto_vibe_cannot_be_refreshed_by_a_user_who_is_not_an_owner_of_the_vibe()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $userTrack = factory(Track::class)->create();
        $user->tracks()->attach($userTrack->id, ['type' => UserAPI::TOP_TRACK]);

        $vibe = factory(Vibe::class)->create(['auto_dj' => true]);
        $vibe->users()->attach($user->id, ['owner' => false]);

        $this->actingAs($user);
        $this->post(route('auto-vibe.refresh', $vibe))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
