<?php

namespace App\Http\Controllers;

use App\MusicAPI\Search;
use App\MusicAPI\Tracks as TracksAPI;

class SearchController extends Controller
{
    public function search(TracksAPI $tracksAPI)
    {
        $input = request('search');
        if(is_null($input)) {
            return redirect()->back();
        }

        $tracks = app(Search::class)->tracks($input);
        return view('search', [
            'apiTracks' => $tracksAPI->check($tracks)
        ]);
    }
}
