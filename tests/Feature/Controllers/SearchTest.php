<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\MusicAPI\Fake\WebAPI as FakeSpotifyWebAPI;
use App\MusicAPI\Tracks as TracksAPI;

class SearchTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_user_can_search_for_tracks()
    {
        $response = $this->get(route('search', ['search' => '2pac']));
        $response->assertViewIs('search');
        $tracks = app(FakeSpotifyWebAPI::class)->search('2pac');
        $apiTracks = app(TracksAPI::class)->check($tracks);
        $response->assertViewHas('apiTracks', $apiTracks);
    }

    public  function test_user_is_redirected_back_if_search_is_empty()
    {
        $response = $this->get(route('search'));
        $response->assertRedirect(route('index'));
    }
}
