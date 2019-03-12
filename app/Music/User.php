<?php

namespace App\Music;

use App\Music\InterfaceAPI;
use App\Music\Tracks;

class User
{
    public const TOP_TRACK = 1;
    public const RECENT_TRACK = 2;

    protected $api;

    public function __construct(InterfaceAPI $interfaceAPI)
    {
        $this->api = $interfaceAPI;
    }

    public function haha()
    {
        return 'bow';
    }

    public function topTracks()
    {
        $tracksList = $this->api->getUserTopTracks();
        foreach ($tracksList as $item) {
            $item->type = self::TOP_TRACK;
        }

        return $tracksList;
    }   

    public function recentTopTracks()
    {
        $tracksList = $this->api->getUserRecentTracks();

        $tracksIDs = [];
        foreach ($tracksList as $item) {
            $tracksIDs[] = $item->track->id;
        }

        $tracks = [];
        $addedTracks = [];
        foreach ($tracksList as $item) {
            foreach (array_count_values($tracksIDs) as $replayedTrackID => $replayCount) {
                if ($item->track->id == $replayedTrackID && $replayCount > 1 && !in_array($item->track->id, $addedTracks)) {
                    $item->track->type = self::RECENT_TRACK;
                    $tracks[] = $item->track;
                    $addedTracks[] = $item->track->id;
                }
            }
        }
        return $tracks;   
    }

    public function recentTopAndOverallTopTracks()
    {
        $tracksList = array_merge($this->topTracks(), $this->recentTopTracks());
        $tracksIDs = [];
        $tracks = [];
        foreach ($tracksList as $item) {
            if (!in_array($item->id, $tracksIDs)) {
                $tracks[] = $item;
            }
            $tracksIDs[] = $item->id;
        }
        return $tracks;
    }

    public function trackSuggestions()
    {
        $userTopTracks = collect($this->topTracks())->pluck('id');
        $userRecentTracks = collect($this->recentTopTracks())->pluck('id');
        
        $tracksAPI = app(Tracks::class);
        $suggestions = $tracksAPI->getRecommendations([
            'target_popularity' => 40,
            'seed_tracks' => array($userRecentTracks[1], $userRecentTracks[2], $userRecentTracks[3]),
            'limit' => 10
        ]);
        return $suggestions->tracks;
    }
}
