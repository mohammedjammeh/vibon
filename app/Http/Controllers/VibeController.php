<?php

namespace App\Http\Controllers;

use App\vibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class VibeController extends Controller
{
    private $spotifyAPI;

    public function __construct()
    {
        $this->middleware('auth');
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vibe.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vibe = new vibe();
        $vibe->user_id = '1';
        $vibe->title = request('title');
        $vibe->description = request('description');
        $vibe->key = '123';
        $vibe->save();


        $this->spotifyAPI->createPlaylist([
            'name' => 'My shiny playlist'
        ]);

        return redirect('/home');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\vibe  $id
     * @return \Illuminate\Http\Response
     */
    public function show(vibe $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\vibe  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(vibe $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\vibe  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, vibe $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\vibe  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(vibe $id)
    {
        //
    }
}
