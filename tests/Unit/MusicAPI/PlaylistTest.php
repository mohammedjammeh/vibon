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

    public function test_the_playlist_load_method_adds_the_expected_attributes_of_a_playlist_to_the_vibe_that_gets_returned()
    {
        $vibe = factory(Vibe::class)->create();
        $playlist = app(Playlist::class)->get($vibe->api_id);

        $loadedVibe = app(Playlist::class)->load($vibe);

        $this->assertEquals($loadedVibe->name, $playlist->name);
        $this->assertEquals($loadedVibe->description, $playlist->description);
        $this->assertEquals($loadedVibe->uri, $playlist->uri);
        $this->assertNotNull($loadedVibe->api_tracks);
        $this->assertNotNull($loadedVibe->synced);
    }

    public function test_the_playlist_load_method_checks_if_a_vibe_is_synced_with_its_playlist_and_returns_false_if_its_not()
    {
        $vibe = factory(Vibe::class)->create();
        $playlist = app(Playlist::class)->get($vibe->api_id);

        $this->assertNotEquals($vibe->tracks->count(), count($playlist->tracks->items));
        $loadedVibe = app(Playlist::class)->load($vibe);
        $this->assertEquals($loadedVibe->synced, false);
    }

    public function test_the_playlist_load_method_checks_if_a_vibe_is_synced_with_its_playlist_and_returns_true_if_it_is()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => false,]);
        $playlist = app(Playlist::class)->get($vibe->api_id);

        foreach($playlist->tracks->items as $item) {
            $track = Track::create(['api_id' => $item['track']->id]);
            $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        }

        $loadedVibe = app(Playlist::class)->load($vibe);
        $this->assertEquals($loadedVibe->synced, true);
    }
}
