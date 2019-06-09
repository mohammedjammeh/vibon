<?php

namespace Tests\Unit\MusicAPI;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Music\User as UserAPI;

class UserTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_top_tracks_method_gets_users_top_tracks_and_adds_a_top_track_type_attribute_to_the_tracks()
    {
        $topTracks = app(UserAPI::class)->topTracks();
        $this->assertNotEmpty($topTracks);
        collect($topTracks)->each(function ($track) {
            $this->assertEquals($track->type, UserAPI::TOP_TRACK);
        });
    }

    public function test_the_recent_top_tracks_method_gets_users_recent_tracks_and_uniquely_selects_ones_played_more_than_and_adds_a_recent_track_type_attribute_to_the_tracks()
    {
        $recentTopTracks = app(UserAPI::class)->recentTopTracks();
        $this->assertCount(2, $recentTopTracks);

        $recentTopTracksIDs = ['recent01', 'recent03']; // Based on Fake/WebAPI
        collect($recentTopTracks)->each(function ($track) use($recentTopTracksIDs) {
            $this->assertContains($track->id, $recentTopTracksIDs);
            $this->assertEquals($track->type, UserAPI::RECENT_TRACK);
        });
    }

    public function test_the_recent_top_and_overall_top_tracks_method_uniquely_merges_top_and_recent_top_tracks_together()
    {
        $userAPI = app(UserAPI::class);
        $recentTopAndOverallTopTracks = $userAPI->recentTopAndOverallTopTracks();
        $topTracks =  $userAPI->topTracks();
        $recentTopTracks = $userAPI->recentTopTracks();

        collect($topTracks)->each(function ($topTrack) use($recentTopAndOverallTopTracks) {
            $this->assertContains($topTrack->id, $recentTopAndOverallTopTracks->pluck('id'));
        });
        collect($recentTopTracks)->each(function ($recentTopTrack) use($recentTopAndOverallTopTracks) {
            $this->assertContains($recentTopTrack->id, $recentTopAndOverallTopTracks->pluck('id'));
        });
        $this->assertEquals($recentTopAndOverallTopTracks->count(), $recentTopAndOverallTopTracks->unique('id')->count());
    }

    public function test_the_track_suggestions_methods_gets_track_suggestions()
    {
        $this->withoutExceptionHandling();
        $trackSuggestions = app(UserAPI::class)->trackSuggestions();
        $this->assertNotEmpty($trackSuggestions);
    }
}
