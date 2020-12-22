<?php

namespace App\Http\Controllers;

use App\MusicAPI\Search;
use App\Traits\VibeShowTrait;

class SearchController extends Controller
{
    use VibeShowTrait;

    public function search($input)
    {
        $tracks = app(Search::class)->tracks($input);
        return  $this->updateTracksVibonInfo($tracks);
    }
}
