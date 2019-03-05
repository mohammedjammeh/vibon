<?php

namespace App\Music;

use App\Music\InterfaceAPI;

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
        foreach ($tracksList as $item) {
            foreach (array_count_values($tracksIDs) as $replayedTrackID => $replayCount) {
                if ($item->track->id == $replayedTrackID && $replayCount > 1) {
                    $item->track->type = self::RECENT_TRACK;
                    $tracks[] = $item->track;
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
}
