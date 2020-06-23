<?php

namespace Tests\Unit\Controllers;

use App\Events\VibeDeleted;
use App\Listeners\StoreAutoVibeTracks;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\User;
use App\Vibe;
use App\Track;
use App\MusicAPI\Playlist;
use App\Events\VibeCreated;
use App\Events\VibeUpdated;
use App\MusicAPI\User as UserAPI;
use App\MusicAPI\Tracks;


class VibeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_vibe_requires_a_name_to_be_created()
    {
        $attributes = factory(Vibe::class)->raw(['name' => '']);
        $this->post(route('vibe.store'), $attributes)->assertSessionHasErrors('name');
    }

    public function test_vibe_requires_a_description_to_be_created()
    {
        $attributes = factory(Vibe::class)->raw(['description' => '']);
        $this->post(route('vibe.store'), $attributes)->assertSessionHasErrors('description');
    }

    public function test_vibe_created_event_is_triggered_when_a_vibe_is_created()
    {
        Event::fake();
        $playlist = app(Playlist::class)->create('Party', 'Everybody have to party!!');
        $attributes = factory(Vibe::class)->raw([
            'api_id' => $playlist->id,
            'name' => $playlist->name,
            'description' => $playlist->description
        ]);
        $this->post(route('vibe.store'), $attributes);
        Event::assertDispatched(VibeCreated::class);
    }

    public function test_vibe_created_listener_ensures_that_new_vibe_has_auto_related_tracks_based_on_its_users_tracks()
    {
        $user = factory(User::class)->create();
        $tracks = factory(Track::class, 2)->create();
        $tracksIDs = $tracks->pluck('id')->toArray();
        $user->tracks()->attach($tracksIDs, ['type' => UserAPI::TOP_TRACK]);
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($user->id, ['owner' => true]);

        $vibeCreatedEvent = new VibeCreated($vibe);
        $vibeCreatedListener = new StoreAutoVibeTracks();
        $vibeCreatedListener->handle($vibeCreatedEvent);

        $vibeAutoTracks = $vibe->tracks()->where('auto_related', true)->get()->pluck('api_id');
        $vibeLoad = $vibe->load('users.tracks');
        $vibeUsersTracks = $vibeLoad['tracks']->pluck('api_id');

        foreach ($vibeAutoTracks as $autoTrack) {
            $this->assertContains($autoTrack, $vibeUsersTracks);
        }
    }

    public function test_vibe_show_view_gets_required_data()
    {
        $this->markTestSkipped('Irrelevant');
        $vibe = factory(Vibe::class)->create();
        $tracks = $vibe->showTracks;
        $loadedTracks = app(Tracks::class)->load($tracks);
        $this->get($vibe->path)->assertViewHasAll([
            'vibe' => app(Playlist::class)->load($vibe),
            'apiTracks' => app(Tracks::class)->check($loadedTracks)
        ]);
    }

    public function test_vibe_is_shown_with_the_right_view()
    {
        $this->markTestSkipped('Irrelevant');
        $vibe = factory(Vibe::class)->create();
        $this->get($vibe->path)->assertViewIs('vibe.show');
    }

    public function test_vibe_updated_event_is_triggered_when_a_vibe_is_updated()
    {
        Event::fake();
        $vibe = factory(Vibe::class)->create();
        $user = factory(User::class)->create();
        $vibe->users()->attach($user->id, ['owner' => true]);

        $this->actingAs($vibe->users->first());
        $this->patch(route('vibe.update', $vibe), [
            'name' => 'Shaka Dance',
            'description' => 'Shakala Boom Boom',
            'open' => $vibe->open,
            'auto_dj' =>  $vibe->auto_dj
        ]);
        Event::assertDispatched(VibeUpdated::class);
    }

    public function test_vibe_deleted_event_is_triggered_when_a_vibe_is_deleted()
    {
        Event::fake();
        $vibe = factory(Vibe::class)->create();
        $user = factory(User::class)->create();
        $vibe->users()->attach($user->id, ['owner' => true]);

        $this->actingAs($vibe->users->first());
        $this->delete(route('vibe.destroy', $vibe));
        Event::assertDispatched(VibeDeleted::class);
    }
}
