<?php

namespace Tests\Feature\Controllers;

use App\Track;
use App\Vibe;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\MusicAPI\Fake\WebAPI as FakeSpotifyWebAPI;
use App\MusicAPI\Tracks as TracksAPI;

class SearchTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_user_can_search_for_tracks_and_receives_a_response_with_the_tracks_updated_vibon_info()
    {
        $this->withoutExceptionHandling();
        $expectedTracksApi = collect(app(FakeSpotifyWebAPI::class)->search('2pac'));
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, [
            'owner' => array_random([true, false])
        ]);

        foreach($expectedTracksApi as $trackAPI) {
            $track = factory(Track::class)->create([
                'api_id' => $trackAPI->id
            ]);
            $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        }

        $searchResponseTracks = $this->get(route('search', ['search' => '2pac']))->original;
        $expectedTracksApiIds = $expectedTracksApi->pluck('id');

        $this->assertEquals($searchResponseTracks->count(), $expectedTracksApi->count());
        foreach ($searchResponseTracks as $track) {
            $this->assertContains($vibe->id, $track->vibes);
            $this->assertContains($track->id, $expectedTracksApiIds);
        }
    }
}
