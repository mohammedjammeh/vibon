<?php

namespace Tests\Feature\Controller;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Vibe;
use App\Music\Search;
use App\Music\Tracks;
use App\Music\Playlist;

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
        $trackSuggestions = app(Search::class)->tracks('Reggae Banton');
        factory(Vibe::class, 2)->create();
        $this->get(route('index'))->assertViewHasAll([
            'apiTracks' => app(Tracks::class)->check($trackSuggestions),
            'vibes' => app(Playlist::class)->loadMany(Vibe::all())
        ]);
    }
}
