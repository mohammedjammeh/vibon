<?php

namespace Tests\Unit\MusicAPI;

use App\MusicAPI\Playlist;
use App\PendingVibeTrack;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Vibe;
use App\Track;
use App\MusicAPI\Tracks;

class TracksTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_tracks_load_for_method_returns_loaded_pending_tracks_to_attach_to_vibe()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $playlist = app(Playlist::class)->get($vibe->api_id);
        $tracks = factory(Track::class, 2)->create();
        foreach($tracks as $track) {
            factory(PendingVibeTrack::class)
                ->states('attach')
                ->create([
                    'vibe_id' => $vibe,
                    'track_id' => $track
                ]);
        }

        $tracksAPI = app(Tracks::class)->loadFor($vibe, $playlist);
        $this->assertEquals($tracksAPI['pending_to_attach']->pluck('id'), $tracks->pluck('api_id'));
    }

    public function test_the_tracks_load_for_method_returns_loaded_pending_tracks_to_detach_from_vibe()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $playlist = app(Playlist::class)->get($vibe->api_id);
        $tracks = factory(Track::class, 2)->create();
        foreach($tracks as $track) {
            factory(PendingVibeTrack::class)
                ->states('detach')
                ->create([
                    'vibe_id' => $vibe,
                    'track_id' => $track
                ]);
        }

        $tracksAPI = app(Tracks::class)->loadFor($vibe, $playlist);
        $this->assertEquals($tracksAPI['pending_to_detach']->pluck('id'), $tracks->pluck('api_id'));
    }

    public function test_the_tracks_load_for_method_returns_loaded_vibe_tracks_that_are_on_the_playlist()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $playlist = app(Playlist::class)->get($vibe->api_id);
        $playlistTracks = collect($playlist->tracks->items)->pluck('track');

        foreach ($playlistTracks as $playlistTrack) {
            $track = factory(Track::class)->create(['api_id' => $playlistTrack->id]);
            $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        }

        $tracksAPI = app(Tracks::class)->loadFor($vibe, $playlist);
        $emptyTracksAPI = $tracksAPI['pending_to_attach']
            ->concat($tracksAPI['pending_to_detach'])
            ->concat($tracksAPI['not_on_playlist'])
            ->concat($tracksAPI['not_on_vibon']);

        $this->assertEmpty($emptyTracksAPI);
        $this->assertEquals(
            $tracksAPI['playlist']->pluck('id'),
            $vibe->showTracks->pluck('api_id')
        );
    }

    public function test_the_tracks_load_for_method_returns_loaded_vibe_tracks_that_are_not_on_the_playlist()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $playlist = app(Playlist::class)->get($vibe->api_id);
        $playlist->tracks->items = [];

        $tracks = factory(Track::class, 2)->create();
        $vibe->tracks()->attach($tracks->pluck('id'), ['auto_related' => false]);

        $tracksAPI = app(Tracks::class)->loadFor($vibe, $playlist);
        $emptyTracksAPI = $tracksAPI['playlist']
            ->concat($tracksAPI['pending_to_attach'])
            ->concat($tracksAPI['playlist'])
            ->concat($tracksAPI['pending_to_detach']);

        $this->assertEmpty($emptyTracksAPI);
        $this->assertEquals(
            $tracksAPI['not_on_playlist']->pluck('id'),
            $vibe->showTracks->pluck('api_id')
        );
    }

    public function test_the_tracks_load_for_method_returns_loaded_playlist_tracks_that_are_not_on_the_vibe()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $playlist = app(Playlist::class)->get($vibe->api_id);

        $tracksAPI = app(Tracks::class)->loadFor($vibe, $playlist);
        $emptyTracksAPI = $tracksAPI['pending_to_attach']
            ->concat($tracksAPI['pending_to_detach'])
            ->concat($tracksAPI['not_on_playlist']);

        $this->assertEmpty($emptyTracksAPI);
        $this->assertEquals(
            $tracksAPI['not_on_vibon']->pluck('id'),
            collect($playlist->tracks->items)->pluck('track')->pluck('id')
        );
    }
    
    public function test_the_tracks_load_method_loads_all_tracks_from_the_api_based_on_given_tracks_from_database()
    {
        $tracks = factory(Track::class, 2)->create();
        $tracksIDsForAPI = $tracks->pluck('api_id');

        $tracksAPI = app(Tracks::class)->load($tracks);

        collect($tracksAPI)->each(function ($trackAPI) use($tracksIDsForAPI) {
            $this->assertContains($trackAPI->id, $tracksIDsForAPI);
            $this->assertObjectHasAttribute('name', $trackAPI);
            $this->assertObjectHasAttribute('artists', $trackAPI);
            $this->assertObjectHasAttribute('artists', $trackAPI);
            $this->assertObjectHasAttribute('uri', $trackAPI);
        });
    }
}