<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreVibe;

use App\Spotify\Playlist;
use App\Spotify\Tracks;
use App\Spotify\WebAPI;

class VibeController extends Controller

{



    public function __construct() {

        parent::__construct();

        $this->middleware('apiAuth', ['only' => ['create', 'edit', 'delete']]);

    }





    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()

    {

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
    public function store(StoreVibe $request, Playlist $playlist)

    {

        $newPlaylist = $playlist->create($request->input('title'));


        $vibe = Vibe::create([

            'title' => request('title'),

            'api_id' => $newPlaylist->id,

            'description' => request('description'),

            'type' => request('type'),

            'auto_dj' => request('auto_dj')

        ]);


        $vibe->users()->attach(Auth::id(), ['owner' => 1]);

        return redirect('/vibe/' . $vibe->id);

    }







    /**
     * Display the specified resource.
     *
     * @param  \App\vibe  $vibe
     * @return \Illuminate\Http\Response
     */
    public function show(Vibe $vibe, Tracks $tracks)

    {

        return view('vibe.show', [

            'vibe' => $vibe, 

            'user' => auth()->user()->load('vibes.tracks'),

            'apiTracks' => $tracks->load($vibe->tracks)
        ]);

    }






    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\vibe  $vibe
     * @return \Illuminate\Http\Response
     */
    public function edit(Vibe $vibe)

    {

        $this->authorize('update', $vibe);

        return view('vibe.edit')->with('vibe', $vibe);

    }






    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\vibe  $vibe
     * @return \Illuminate\Http\Response
     */
    public function update(StoreVibe $request, Vibe $vibe, Playlist $playlist)

    {

        $this->authorize('update', $vibe);
        

        $vibe->update(request(['title', 'type', 'auto_dj', 'description']));

        $playlist->update($vibe->api_id, $request->input('title'));


        return redirect('/vibe/' . $vibe->id);

    }







    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\vibe  $vibe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vibe $vibe, Playlist $playlist)

    {

        $this->authorize('delete', $vibe);


        $playlist->delete($vibe->api_id);

        $message = $vibe->title . ' has been deleted.';

        $vibe->users()->detach(Auth::id());

        $vibe->delete();


        return redirect('/home')->with('message', $message);

    }


}
