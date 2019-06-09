<?php

namespace Tests\Unit\AutoDJ;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use App\Track;
use App\MusicAPI\User as UserAPI;
use App\AutoDJ\User as AutoUser;
use App\MusicAPI\Fake\WebAPI as FakeAPI;

class UserTest extends TestCase
{
	use WithFaker, RefreshDatabase;

    public function test_authenticated_user_top_tracks_from_the_api_are_stored()
    {
        $this->user->tracks()->detach();
        AutoUser::storeTracks();

        $userTopTracksAPI = app(FakeAPI::class)->getUserTopTracks();
        foreach ($userTopTracksAPI as $userTopTrackAPI) {
            $userTopTrack = Track::where('api_id', $userTopTrackAPI->id)->first();
            $this->assertDatabaseHas('user_track', [
                'user_id' => $this->user->id,
                'track_id' => $userTopTrack->id,
                'type' => UserAPI::TOP_TRACK
            ]);
        }
    }

    public function test_authenticated_user_recent_tracks_from_the_api_that_have_been_played_more_than_once_are_stored()
    {
        $this->user->tracks()->detach();
        AutoUser::storeTracks();

        $userRecentTracksAPI = app(FakeAPI::class)->getUserRecentTracks();
        $userRecentTracksIDs = Arr::pluck($userRecentTracksAPI, 'track.id');
        $userRecentTracksCount = array_count_values($userRecentTracksIDs);
        
        $tracksPlayedMoreThanOnce = Arr::where($userRecentTracksCount, function ($value, $key) {
            return $value > 1;
        });
        foreach ($tracksPlayedMoreThanOnce as $trackID => $trackCount) {
            $userTopRecentTrack = Track::where('api_id', $trackID)->first();
            $this->assertDatabaseHas('user_track', [
                'user_id' => $this->user->id,
                'track_id' => $userTopRecentTrack->id,
                'type' => UserAPI::RECENT_TRACK
            ]);
        }
    }

    public function test_authenticated_user_tracks_from_api_can_be_updated()
    {
        $userOldTracks = $this->user->tracks;
        AutoUser::updateTracks();

        foreach ($userOldTracks as $userOldTrack) {
            $this->assertDatabaseMissing('user_track', [
                'user_id' => $userOldTrack->pivot->user_id,
                'track_id' => $userOldTrack->pivot->user_id,
                'type' => $userOldTrack->pivot->type
            ]);
        }
        
        $userTopTracksAPI = app(FakeAPI::class)->getUserTopTracks();
        foreach ($userTopTracksAPI as $userTopTrackAPI) {
            $userTopTrack = Track::where('api_id', $userTopTrackAPI->id)->first();
            $this->assertDatabaseHas('user_track', [
                'user_id' => $this->user->id,
                'track_id' => $userTopTrack->id,
                'type' => UserAPI::TOP_TRACK
            ]);
        }
    }
}
