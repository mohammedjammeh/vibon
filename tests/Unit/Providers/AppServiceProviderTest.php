<?php

namespace Tests\Unit\Providers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\MusicAPI\Playlist;
use App\Vibe;

class AppServiceProviderTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->app->extend('SpotifySession', function () {
            return new Session();
        });
    }

    public function test_all_views_have_the_user_variable()
    {
        $this->markTestSkipped('Irrelevant');
        $user = $this->user->load('vibes.tracks');
        app(Playlist::class)->loadMany($user['vibes']);
        $home = $this->get(route('index'));
        $home->assertViewHas('user', $user);

        $vibe = factory(Vibe::class)->create();
        $vibeShowPage = $this->get($vibe->path);
        $vibeShowPage->assertViewHas('user', $user);
    }
}
