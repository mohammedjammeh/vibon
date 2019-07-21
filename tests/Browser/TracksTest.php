<?php

namespace Tests\Browser\Includes;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Vibe;
use App\User;
use App\Track;
use App\MusicAPI\User as UserAPI;

class TracksTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_tracks_which_belong_to_the_vibes_that_a_user_is_a_member_of_are_identified_as_tracks_that_can_be_removed()
    {
        $user = factory(User::class)->create();
        $vibe = factory(Vibe::class)->create();
        $user->vibes()->attach($vibe->id, ['owner' => false]);
        $trackSuggestions = app(UserAPI::class)->trackSuggestions();
        $track = factory(Track::class)->create([
            'api_id' => collect($trackSuggestions)->first()->id
        ]);
        $vibe->tracks()->attach($track->id, ['auto_related' => 0]);

        $this->browse(function (Browser $browser) use($user, $vibe) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $browser->loginAs($user)
                ->visit(route('index'));
            $selector = 'form input[name="track-vibe-destroy"]';
            $browser->assertInputValue($selector, $vibe->name);
        });
    }

    public function test_tracks_which_do_not_belong_to_the_vibes_that_a_user_is_a_member_of_are_identified_as_tracks_that_can_be_stored()
    {
        $user = factory(User::class)->create();
        $vibe = factory(Vibe::class)->create();
        $user->vibes()->attach($vibe->id, ['owner' => false]);

        $this->browse(function (Browser $browser) use($user, $vibe) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $browser->loginAs($user)
                ->visit(route('index'));
            $selector = 'form input[name="track-vibe-store"]';
            $browser->assertInputValue($selector, $vibe->name);
        });
    }
}
