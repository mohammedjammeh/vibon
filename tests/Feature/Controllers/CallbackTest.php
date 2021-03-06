<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\MusicAPI\Fake\Session;
use App\MusicAPI\User as UserAPI;
use App\User;
use Illuminate\Support\Facades\Auth;


class CallbackTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        Auth::logout();
        User::truncate();
        $this->app->extend('SpotifySession', function () {
            return new Session();
        });
    }

    public function test_spotify_callback_stores_a_new_users_details_and_authenticates_him()
    {
        $response = $this->get(route('callback.spotify') . '?code=123');
        $this->assertCount(1, User::all());
        $this->assertNotEmpty(User::first()->tracks);
        $this->assertAuthenticatedAs(User::first(), $guard = null);
        $response->assertRedirect(route('index'));
    }

    public function test_spotify_callback_updates_a_previously_authenticated_users_details_instead_of_adding_him_as_new()
    {
        $user = factory(User::class)->create(['api_id' => app(UserAPI::class)->details()->id]);
        $this->assertEmpty($user->tracks);
        $this->get(route('callback.spotify') . '?code=123');
        $this->assertNotEmpty(User::first()->tracks);
    }
}