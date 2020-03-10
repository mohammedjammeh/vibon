<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Vibe;
use App\MusicAPI\Search;
use App\MusicAPI\Tracks;
use App\MusicAPI\Playlist;
use App\MusicAPI\User as UserAPI;

class HomeTest extends TestCase
{
	use WithFaker, RefreshDatabase;

    public function test_home_can_be_viewed_by_a_user()
    {
        $this->get(route('index'))
            ->assertSuccessful();
    }

    public function test_home_is_shown_with_the_right_view() 
    {
        $this->get(route('index'))->assertViewIs('home');
    }

    public function test_home_view_gets_required_data()
    {
        $this->markTestSkipped('Irrelevant');
//        $trackSuggestions = app(Search::class)->tracks('Reggae Banton');
        $trackSuggestions = app(UserAPI::class)->trackSuggestions();
        factory(Vibe::class, 2)->create();
        $this->get(route('index'))->assertViewHasAll([
            'apiTracks' => app(Tracks::class)->check($trackSuggestions),
            'vibes' => app(Playlist::class)->loadMany(Vibe::all())
        ]);
    }
}
