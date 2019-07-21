<?php

namespace Tests\Browser\Vibe;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Vibe;

class EditPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_vibe_member_can_edit_a_vibe()
    {
        $user = factory(User::class)->create();
        $vibe = factory(Vibe::class)->create();
        $user->vibes()->attach($vibe->id, ['owner' => false]);

        $this->browse(function (Browser $browser) use($user, $vibe) {
            $browser->loginAs($user)
                ->visit(route('vibe.edit', ['vibe' => $vibe->id]))
                ->clear('description')
                ->type('description', 'Welcome to Yoo Party!')
                ->press('Update')
                ->assertUrlIs($vibe->path)
                ->assertSee('Welcome to Yoo Party!');
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
