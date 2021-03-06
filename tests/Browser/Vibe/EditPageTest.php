<?php

namespace Tests\Browser\Vibe;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Vibe;
use App\MusicAPI\Playlist;

class EditPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_vibe_member_can_edit_a_vibe()
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $vibe = factory(Vibe::class)->create();
            $user->vibes()->attach($vibe->id, ['owner' => false]);
            $playlist = app(Playlist::class)->get('23n32ndw923njn23');

            $browser->loginAs($user)
                ->visit(route('vibe.edit', ['vibe' => $vibe->id]))
                ->clear('name')
                ->type('name', $playlist->name)
                ->clear('description')
                ->type('description', $playlist->description)
                ->select('open', '0')
                ->select('auto_dj', '0')
                ->press('Update')
                ->assertUrlIs($vibe->path)
                ->assertSee($playlist->name)
                ->assertSee($playlist->description)
                ->assertSee('Manual DJ')
                ->assertSee('Not Opened');
        });
    }

    public function test_non_vibe_member_cannot_edit_a_vibe()
    {
        $user = factory(User::class)->create();
        $vibe = factory(Vibe::class)->create();

        $this->browse(function (Browser $browser) use($user, $vibe) {
            $updateVibeBtn = 'input[name="vibe-update"]';
            $browser->loginAs($user)
                ->visit(route('vibe.edit', ['vibe' => $vibe->id]))
                ->assertMissing($updateVibeBtn)
                ->assertSee(403);
        });
    }

    public function test_error_is_displayed_on_the_page_if_vibe_update_validation_fails()
    {
        $user = factory(User::class)->create();
        $vibe = factory(Vibe::class)->create();
        $user->vibes()->attach($vibe->id, ['owner' => true]);

        $this->browse(function (Browser $browser) use($user, $vibe) {
            $browser->loginAs($user)
                ->visit(route('vibe.edit', ['vibe' => $vibe->id]))
                ->clear('description')
                ->type('description', '')
                ->press('Update')
                ->assertUrlIs(route('vibe.edit', ['vibe' => $vibe->id]))
                ->assertSee('The description field is required');
        });
    }
}
