<?php

namespace App\Http\Controllers;

use App\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class TrackController extends Controller
{
    private $spotifyAPI;

    public function __construct()
    {
        $this->middleware('spotifySession');

        $this->middleware('spotifyAuth', ['only' => ['create', 'store', 'edit', 'delete']]);

        $this->middleware(function ($request, $next) {
            $this->spotifyAPI = new SpotifyWebAPI();

            if (!Session::has('accessToken')) {
                $this->spotifyAPI->setAccessToken(Session::get('credentialsToken'));
            } else {
                $this->spotifyAPI->setAccessToken(Session::get('accessToken'));
            }

            return $next($request);
        });


    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        print_r(
            $this->spotifyAPI->getTrack('7EjyzZcbLxW7PaaLua9Ksb')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function show(Track $track)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function edit(Track $track)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Track $track)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function destroy(Track $track)
    {
        //
    }
}
