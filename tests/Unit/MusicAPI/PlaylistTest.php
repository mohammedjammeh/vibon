<?php

namespace Tests\Unit\MusicAPI;

use App\Track;
use App\Vibe;
use App\MusicAPI\Playlist;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaylistTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_playlist_load_method_checks_if_a_vibe_is_synced_with_its_playlist_and_returns_false_if_its_not()
    {
        $playlist = app(Playlist::class)->create('Party', 'Everybody have to partyy');
        $vibe = factory(Vibe::class)->create(['api_id' => $playlist->id]);

        $this->assertNotEquals($vibe->tracks->count(), count($playlist->tracks->items));
        $loadedVibe = app(Playlist::class)->load($vibe);
        $this->assertEquals($loadedVibe->synced, false);
    }

    public function test_the_playlist_load_method_checks_if_a_vibe_is_synced_with_its_playlist_and_returns_true_if_it_is()
    {
        $playlist = app(Playlist::class)->create('Party', 'Everybody have to party.');
        $vibe = factory(Vibe::class)->create([
            'api_id' => $playlist->id,
            'auto_dj' => false,
        ]);

        foreach($playlist->tracks->items as $item) {
            $track = Track::create(['api_id' => $item['track']->id]);
            $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        }

        $loadedVibe = app(Playlist::class)->load($vibe);
        $this->assertEquals($loadedVibe->synced, true);
    }
}
