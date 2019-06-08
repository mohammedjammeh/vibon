<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Music\Fake\Session;
use App\Music\Fake\WebAPI;
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
        $fakeUser = app(WebAPI::class)->getuser();
        factory(User::class)->create([
            'username' => $fakeUser->id
        ]);

        $this->get(route('callback.spotify') . '?code=123');
        $this->assertCount(1, User::all());
        $this->assertEquals(User::first()->email, $fakeUser->email);
    }
}