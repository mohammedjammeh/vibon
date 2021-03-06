<?php

namespace Tests\Browser\Vibe;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\MusicAPI\Playlist;

class CreatePageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_create_a_vibe()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $playlist = app(Playlist::class)->get('23n32ndw923njn23');

            $browser->loginAs($user)
                ->visit(route('vibe.create'))
                ->type('name', $playlist->name)
                ->type('description', $playlist->description)
                ->select('open', '0')
                ->select('auto_dj', '0')
                ->press('Start')
                ->assertUrlIs(route('vibe.show', ['vibe' => 1]))
                ->assertSee($playlist->name)
                ->assertSee($playlist->description)
                ->assertSee('Manual DJ')
                ->assertSee('Not Opened');
        });
    }

    public function test_error_is_displayed_on_the_page_if_validation_fails()
    {
        $user = factory(User::class)->create();
        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($user)
                ->visit(route('vibe.create'))
                ->type('name', '')
                ->select('open', '1')
                ->select('auto_dj', '0')
                ->type('description', 'Welcome to Yoo Party!')
                ->press('Start')
                ->assertUrlIs(route('vibe.create'))
                ->assertSee('The name field is required');
        });
    }
}
