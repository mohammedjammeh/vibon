<?php

namespace Tests\Unit\Controllers;

use App\Events\PlaylistSynchronisedWithVibeTracks;
use App\Events\VibeSynchronisedWithPlaylistTracks;
use App\MusicAPI\Playlist;
use App\Vibe;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VibeSynchronisationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_that_the_vibe_synchronised_with_playlist_tracks_event_is_triggered_when_user_synchronises_vibe_with_playlist_tracks()
    {
        Event::fake();

        $playlist = app(Playlist::class)->create('Party', 'Everybody have to party');
        $vibe = factory(Vibe::class)->create([
            'api_id' => $playlist->id,
            'auto_dj' => true,
        ]);

        $this->post(route('vibe-sync.playlist', ['vibe' => $vibe]));

        Event::assertDispatched(VibeSynchronisedWithPlaylistTracks::class);
    }

    public function test_that_the_playlist_synchronised_with_vibe_tracks_event_is_triggered_when_user_synchronises_playlist_with_vibe_tracks()
    {
        Event::fake();

        $playlist = app(Playlist::class)->create('Party', 'Everybody have to party');
        $vibe = factory(Vibe::class)->create([
            'api_id' => $playlist->id,
            'auto_dj' => true,
        ]);

        $this->post(route('vibe-sync.vibe', ['vibe' => $vibe]));

        Event::assertDispatched(PlaylistSynchronisedWithVibeTracks::class);
    }
}
