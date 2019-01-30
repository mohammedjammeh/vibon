<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use App\Spotify\WebAPI;

class Controller extends BaseController

{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;



    public function __construct()

    {

        $this->middleware('auth');

        new WebAPI();

    }
    
}

