<?php

namespace Tests\Unit\MusicAPI;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Vibe;
use App\Track;
use App\MusicAPI\Tracks;

class TracksTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_tracks_load_method_loads_all_tracks_from_the_api_based_on_given_tracks_from_database()
    {
        $tracks = factory(Track::class, 2)->create();
        $tracksIDsForAPI = $tracks->pluck('api_id');
        $tracksAPI = app(Tracks::class)->load($tracks);

        collect($tracksAPI)->each(function ($trackAPI) use($tracksIDsForAPI) {
            $this->assertContains($trackAPI->id, $tracksIDsForAPI);
            $this->assertObjectHasAttribute('name', $trackAPI);
            $this->assertObjectHasAttribute('artists', $trackAPI);
        });
    }
}