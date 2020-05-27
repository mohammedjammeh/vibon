<?php

namespace Tests\Feature\Middlewares;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use App\Track;
use App\MusicAPI\User as UserAPI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Middleware\MusicAPI\checkIfAuthenticated as checkIfAuthenticatedMiddleware;

class SetAccessTokenTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->app->extend('SpotifySession', function () {
            return new Session();
        });
    }

    public function test_unauthorised_user_is_redirected_to_welcome_page_by_set_access_token_middleware()
    {
        Auth::logout();
        $request = Request::create(route('index'), 'GET');
        $response = app(checkIfAuthenticatedMiddleware::class)->handle($request, function () {});
        $this->assertEquals($response->getStatusCode(), 302);
    }

    public function test_user_tracks_are_updated_by_set_access_token_middleware_if_his_first_track_was_attached_more_than_24_hours_ago()
    {
        $track = factory(Track::class)->create();
        $track->users()->attach($this->user->id, [
            'type' => UserAPI::TOP_TRACK,
            'updated_at' => Carbon::now()->subDays(2),
            'created_at' => Carbon::now()->subDays(2)
        ]);

        $request = Request::create(route('index'), 'GET');
        app(checkIfAuthenticatedMiddleware::class)->handle($request, function () {});

        $newTracks = $this->user->tracks->pluck('api_id');
        $this->assertNotContains($track->api_id, $newTracks);
        $this->assertNotEmpty($newTracks);
    }
}
