<?php

namespace Tests\Unit\Traits;

use App\MusicAPI\Playlist;
use App\PendingVibeTrack;
use App\Track;
use App\Traits\VibeShowTrait;
use App\User;
use App\Vibe;
use App\Vote;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VibeShowTest extends TestCase
{
    use WithFaker, RefreshDatabase, VibeShowTrait;

    public function test_the_show_response_method_returns_the_loaded_vibe_with_its_updated_attributes_and_a_message()
    {
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = $loadedVibe->name . ' has been loaded.';

        $vibeShow = $this->showResponse($loadedVibe, $message);

        $this->assertTrue($vibeShow['vibe']->destroyable);
        $this->assertEmpty($vibeShow['vibe']->requests);
        $this->assertTrue($vibeShow['vibe']->currentUserIsAMember);
        $this->assertFalse($vibeShow['vibe']->hasJoinRequestFromUser);
        $this->assertNull($vibeShow['vibe']->joinRequestFromUser);
        $this->assertEmpty($vibeShow['vibe']->notifications());
        $this->assertEquals($vibeShow['message'], $message);
    }

    public function test_the_show_response_method_adds_vibon_id_attribute_to_the_tracks_of_a_loaded_vibe()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $playlist = app(Playlist::class)->get($vibe->api_id);
        $playlistTrackID = collect($playlist->tracks->items)->pluck('track')->first()->id;

        $track = factory(Track::class)->create(['api_id' => $playlistTrackID]);
        $vibe->tracks()->attach($track->id, ['auto_related' => false]);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $vibeShow = $this->showResponse($loadedVibe);

        $this->assertEquals(
            $vibeShow['vibe']->api_tracks['playlist']->first()->vibon_id,
            $track->id
        );
    }

    public function test_the_show_response_method_adds_the_tracks_vibes_to_the_tracks_of_a_loaded_vibe_based_on_the_current_users_vibes()
    {
        $vibes = factory(Vibe::class, 2)->create(['auto_dj' => false]);
        $vibeID = array_random([$vibes->first()->api_id, $vibes->last()->api_id]);
        $playlist = app(Playlist::class)->get($vibeID);
        $playlistTracksIDs = collect($playlist->tracks->items)->pluck('track')->pluck('id');

        $tracks = $playlistTracksIDs->map(function ($trackID) {
            return factory(Track::class)->create(['api_id' => $trackID]);
        });

        $this->user->vibes()->attach($vibes->pluck('id'));

        $vibes->first()->tracks()->attach($tracks->pluck('id'), ['auto_related' => false]);
        $vibes->last()->tracks()->attach($tracks->pluck('id'), ['auto_related' => false]);

        $loadedVibe = app(Playlist::class)->load($vibes->first());
        $vibeShow = $this->showResponse($loadedVibe);
        $loadedTrack = $vibeShow['vibe']->api_tracks['playlist']->first();

        $this->assertContains($vibes->first()->id, $loadedTrack->vibes);
        $this->assertContains($vibes->last()->id, $loadedTrack->vibes);
    }

    public function test_the_show_response_method_adds_pending_vibes_to_attach_attribute_to_the_tracks_of_a_loaded_vibe()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $playlist = app(Playlist::class)->get($vibe->api_id);
        $playlistTrackID = collect($playlist->tracks->items)->pluck('track')->first()->id;

        $track = factory(Track::class)->create(['api_id' => $playlistTrackID]);
        $track->vibes()->attach($vibe->id, ['auto_related' => false]);

        $user = factory(User::class)->create();
        $pendingVibes = factory(Vibe::class, 2)->create();
        $user->vibes()->attach($pendingVibes->pluck('id'), ['owner' => true]);
        factory(PendingVibeTrack::class)->states('attach')->create([
           'track_id' => $track,
           'vibe_id' => $pendingVibes->first(),
           'user_id' => $user
        ]);
        factory(PendingVibeTrack::class)->states('attach')->create([
            'track_id' => $track,
            'vibe_id' => $pendingVibes->last(),
            'user_id' => $user
        ]);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $vibeShow = $this->showResponse($loadedVibe);
        $loadedTrack = $vibeShow['vibe']->api_tracks['playlist']->first();

        $this->assertContains($pendingVibes->first()->id, $loadedTrack->pending_vibes_to_attach);
        $this->assertContains($pendingVibes->last()->id, $loadedTrack->pending_vibes_to_attach);
    }

    public function test_the_show_response_method_adds_pending_vibes_to_detach_attribute_to_the_tracks_of_a_loaded_vibe()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $playlist = app(Playlist::class)->get($vibe->api_id);
        $playlistTrackID = collect($playlist->tracks->items)->pluck('track')->first()->id;

        $track = factory(Track::class)->create(['api_id' => $playlistTrackID]);
        $track->vibes()->attach($vibe->id, ['auto_related' => false]);

        $user = factory(User::class)->create();
        $pendingVibes = factory(Vibe::class, 2)->create();
        $user->vibes()->attach($pendingVibes->pluck('id'), ['owner' => true]);
        factory(PendingVibeTrack::class)->states('detach')->create([
            'track_id' => $track,
            'vibe_id' => $pendingVibes->first(),
            'user_id' => $user
        ]);
        factory(PendingVibeTrack::class)->states('detach')->create([
            'track_id' => $track,
            'vibe_id' => $pendingVibes->last(),
            'user_id' => $user
        ]);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $vibeShow = $this->showResponse($loadedVibe);
        $loadedTrack = $vibeShow['vibe']->api_tracks['playlist']->first();

        $this->assertContains($pendingVibes->first()->id, $loadedTrack->pending_vibes_to_detach);
        $this->assertContains($pendingVibes->last()->id, $loadedTrack->pending_vibes_to_detach);
    }

    public function test_the_show_response_method_adds_votes_count_attribute_to_the_tracks_of_a_loaded_vibe_based_on_the_vibe()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $playlist = app(Playlist::class)->get($vibe->api_id);
        $playlistTrackID = collect($playlist->tracks->items)->pluck('track')->first()->id;

        $track = factory(Track::class)->create(['api_id' => $playlistTrackID]);
        $track->vibes()->attach($vibe->id, ['auto_related' => false]);

        $votes = factory(Vote::class, 2)->create([
            'track_id' => $track,
            'vibe_id' => $vibe
        ]);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $vibeShow = $this->showResponse($loadedVibe);

        $this->assertEquals(
            $vibeShow['vibe']->api_tracks['playlist']->first()->votes_count,
            $votes->count()
        );
    }

    public function test_the_show_response_method_adds_is_voted_by_user_attribute_to_the_tracks_of_a_loaded_vibe_and_returns_true_if_voted_by_user()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $playlist = app(Playlist::class)->get($vibe->api_id);
        $playlistTrackID = collect($playlist->tracks->items)->pluck('track')->first()->id;

        $track = factory(Track::class)->create(['api_id' => $playlistTrackID]);
        $track->vibes()->attach($vibe->id, ['auto_related' => false]);

        factory(Vote::class)->create([
            'track_id' => $track,
            'vibe_id' => $vibe,
            'user_id' => $this->user
        ]);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $vibeShow = $this->showResponse($loadedVibe);

        $this->assertEquals(
            $vibeShow['vibe']->api_tracks['playlist']->first()->is_voted_by_user,
            true
        );
    }

    public function test_the_show_response_method_adds_is_voted_by_user_attribute_to_the_tracks_of_a_loaded_vibe_and_returns_false_if_not_voted_by_user()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $playlist = app(Playlist::class)->get($vibe->api_id);
        $playlistTrackID = collect($playlist->tracks->items)->pluck('track')->first()->id;

        $track = factory(Track::class)->create(['api_id' => $playlistTrackID]);
        $track->vibes()->attach($vibe->id, ['auto_related' => false]);

        factory(Vote::class)->create([
            'track_id' => $track,
            'vibe_id' => $vibe
        ]);

        $loadedVibe = app(Playlist::class)->load($vibe);
        $vibeShow = $this->showResponse($loadedVibe);

        $this->assertEquals(
            $vibeShow['vibe']->api_tracks->flatten()->first()->is_voted_by_user,
            false
        );
    }
}
