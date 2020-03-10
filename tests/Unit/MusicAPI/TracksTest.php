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

    public function test_the_tracks_check_method_adds_all_the_vibes_of_the_authenticated_user_to_a_track_which_that_track_belongs_to_and_also_adds_that_tracks_vibon_id()
    {
        $this->markTestSkipped('Irrelevant');
        $user = $this->user;
        $vibe = factory(Vibe::class)->create();
        $tracks = factory(Track::class, 2)->create();
        $user->vibes()->attach($vibe->id, ['owner' => false]);
        $vibe->tracks()->attach($tracks->pluck('id'), ['auto_related' => 0]);

        $tracksAPI = app(Tracks::class)->load($tracks);
        $tracksChecked = app(Tracks::class)->check($tracksAPI);
        $tracksChecked->each(function ($trackChecked) use($vibe, $tracks) {
            $this->assertContains($vibe->id, $trackChecked->vibes);
            $track = $tracks->where('api_id', $trackChecked->id)->first();
            $this->assertEquals($track->id, $trackChecked->vibon_id);
        });
    }
}