<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Vibe;
use App\User;
use App\JoinRequest;
use App\MusicAPI\User as UserAPI;
use App\Notifications\ResponseToJoinAVibe;
use App\Notifications\RemovedFromAVibe;

class HomeTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_authenticated_user_can_access_home_page()
    {
        $user = factory(User::class)->create();
        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($user)
                ->visit(route('home'))
                ->assertUrlIs(route('home'))
                ->assertSeeLink('Start a vibe')
                ->assertAuthenticatedAs($user);
        });
    }

    public function test_user_is_redirected_to_the_vibe_create_page_when_he_clicks_the_start_a_vibe_link()
    {
        $user = factory(User::class)->create();
        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($user)
                ->visit(route('home'))
                ->clickLink('Start a vibe')
                ->assertUrlIs(route('vibe.create'))
                ->assertPathIs('/vibe/create');
        });
    }

    public function test_user_whose_join_request_has_been_accepted_can_see_this_notification_on_the_home_page()
    {
        $user = factory(User::class)->create();
        $vibe = factory(Vibe::class)->create();
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $user->id
        ]);
        $joinRequest->user->notify(new ResponseToJoinAVibe($joinRequest->vibe->id, true));

        $this->loadVibesAsPlaylists(collect([$vibe]))->first();
        $this->browse(function (Browser $browser) use($user, $vibe) {
            $browser->loginAs($user)
                ->visit(route('index'))
                    ->assertSee("Your request to join '" . $vibe->name . "' has been accepted.");
        });
    }

    public function test_user_whose_join_request_has_been_rejected_can_see_this_notification_on_the_home_page()
    {
        $user = factory(User::class)->create();
        $vibe = factory(Vibe::class)->create();
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $user->id
        ]);
        $joinRequest->user->notify(new ResponseToJoinAVibe($joinRequest->vibe->id, false));

        $this->loadVibesAsPlaylists(collect([$vibe]))->first();
        $this->browse(function (Browser $browser) use($user, $vibe) {
            $browser->loginAs($user)
                ->visit(route('index'))
                ->assertSee("Sorry, your request to join '" . $vibe->name . "' has been rejected.");
        });
    }

    public function test_user_who_has_been_removed_from_a_vibe_can_see_this_notification_on_the_home_page()
    {
        $vibe = factory(Vibe::class)->create();
        $user = factory(User::class)->create();
        $user->notify(new RemovedFromAVibe($vibe->id));

        $this->loadVibesAsPlaylists(collect([$vibe]))->first();
        $this->browse(function (Browser $browser) use($user, $vibe) {
            $browser->loginAs($user)
                ->visit(route('index'))
                ->assertSee("You have been removed from the '" . $vibe->name . "' vibe.");
        });
    }

    public function test_a_users_vibes_are_shown_on_the_home_page_with_their_number_of_join_requests()
    {
        $user = factory(User::class)->create();
        $vibes = factory(Vibe::class, 2)->create();
        $user->vibes()->attach($vibes->pluck('id'), ['owner' => true]);
        factory(JoinRequest::class, 5)->create(['vibe_id' => $vibes->last()->id]);

        $this->loadVibesAsPlaylists($vibes);
        $this->browse(function (Browser $browser) use($user, $vibes) {
            $browser->loginAs($user)
                ->visit(route('index'))
                ->assertSee($vibes->first()->name)
                ->assertSee($vibes->last()->name  . ' (5)');
        });
    }

//    public function test_track_suggestions_are_displayed_on_home_page()
//    {
//        $user = factory(User::class)->create();
//        $trackSuggestions = app(UserAPI::class)->trackSuggestions();
//
//        $this->browse(function (Browser $browser) use($user, $trackSuggestions) {
//            foreach($trackSuggestions as $trackSuggestion) {
//                $browser->loginAs($user)
//                    ->visit(route('index'))
//                    ->assertSee($trackSuggestion->name);
//            }
//        });
//    }
}
