<?php

namespace App\Http\Controllers;

use App\MusicAPI\Search;
use App\MusicAPI\Tracks as TracksAPI;

class SearchController extends Controller
{
    public function search($input)
    {
        $tracks = app(Search::class)->tracks($input);
        return  app(TracksAPI::class)->updateTracksVibonInfo($tracks);
    }
}
