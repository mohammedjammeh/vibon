<?php

namespace Tests\Feature\Controllers;

use App\Track;
use App\Vibe;
use App\MusicAPI\Playlist;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VibeSynchronisationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_vibe_and_spotify_playlist_can_be_synchronised_using_tracks_on_playlist()
    {
        $playlist = app(Playlist::class)->create('Party', 'Everybody have to party');
        $playlistTracks = collect($playlist->tracks->items)->pluck('track');
        $vibe = factory(Vibe::class)->create([
            'api_id' => $playlist->id,
            'auto_dj' => true,
        ]);

        $this->post(route('vibe.sync', ['vibe' => $vibe]),
            ['playlist' => 'Playlist']);

        $this->assertEquals($vibe->tracks->count(), $playlistTracks->count());

        $playlistTracksIDs = $playlistTracks->pluck('id');
        $vibeTracksIDs = Track::whereIn('api_id', $playlistTracksIDs)->get()->pluck('id');
        foreach($vibeTracksIDs as $vibeTrackID) {
            $this->assertDatabaseHas('track_vibe', [
                'track_id' => $vibeTrackID,
                'vibe_id' => $vibe->id
            ]);
        }
    }
}
