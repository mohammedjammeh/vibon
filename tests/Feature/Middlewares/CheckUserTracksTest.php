<?php

namespace Tests\Feature\Middlewares;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Track;
use App\MusicAPI\User as UserAPI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckUserTracks;

class CheckUserTracksTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->app->extend('SpotifySession', function () {
            return new Session();
        });
    }

    public function test_user_tracks_are_checked_and_updated_by_the_middleware_if_his_first_track_was_created_more_than_24_hours_ago()
    {
        $track = factory(Track::class)->create();
        $track->users()->attach($this->user->id, [
            'type' => UserAPI::TOP_TRACK,
            'updated_at' => Carbon::now()->subDays(2),
            'created_at' => Carbon::now()->subDays(2)
        ]);

        $request = Request::create(route('index'), 'GET');
        app(CheckUserTracks::class)->handle($request, function () {});

        $newTracks = $this->user->tracks->pluck('api_id');
        $this->assertNotContains($track->api_id, $newTracks);
        $this->assertNotEmpty($newTracks);
    }
}
