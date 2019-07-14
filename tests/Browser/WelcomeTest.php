<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class WelcomeTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_an_unauthenticated_user_accesses_the_welcome_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('index'))
                ->assertSee('SPOTIFY');
        });
    }

    public function test_an_unauthenticated_user_can_be_authorised_with_spotify_by_being_redirected_to_the_spotify_authorisation_url()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('index'))
                ->click('.spotifyBtn')
                ->assertUrlIs('https://accounts.spotify.com/en/login');
        });
    }

    public function test_an_authenticated_user_who_tries_to_access_the_welcome_page_is_redirected_to_the_home_page()
    {
        $user = factory(User::class)->create();
        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($user)
                ->visit(route('welcome'))
                ->assertUrlIs(route('home'))
                ->assertPathIs('/home')
                ->assertAuthenticatedAs($user);
        });
    }
}
