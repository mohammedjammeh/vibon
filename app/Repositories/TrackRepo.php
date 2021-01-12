<?php

namespace App\Repositories;

use App\Track;
use App\AutoDJ\Genre as AutoGenre;

/**
 * Class Track Repository.
 */
class TrackRepo
{
    public function create($trackApiId)
    {
        $track = Track::where('api_id', $trackApiId)->first();
        if(! is_null($track)) {
            return $track;
        }

        $track = Track::create(['api_id' => $trackApiId]);
        AutoGenre::store($track);
        return $track;
    }
}
