<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;

use App\Music\Playlist;
use App\Music\Tracks as TracksAPI;
use App\Music\User as UserAPI;
use App\Music\Artist as ArtistAPI;
use App\Music\Spotify\WebAPI;
use App\Events\VibeCreated;
use App\Events\VibeUpdated;

use App\Http\Requests\StoreVibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VibeController extends Controller
{
    public function __construct() {
        $this->middleware('setAccessTokenForAPI');
        $this->middleware('checkAuthorisationForAPI', ['only' => ['create', 'edit', 'delete']]);
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
    public function create(WebAPI $webAPI)
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
        $newPlaylist = $playlist->create($request->input('name'));
        $vibe = Vibe::create([
            'api_id' => $newPlaylist->id,
            'description' => request('description'),
            'open' => request('open'),
            'auto_dj' => request('auto_dj')
        ]);
        $vibe->users()->attach(Auth()->user()->id, ['owner' => 1]);
        event(new VibeCreated($vibe));
        return redirect($vibe->path());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\vibe  $vibe
     * @return \Illuminate\Http\Response
     */
    public function show(Vibe $vibe, Playlist $playlist, TracksAPI $tracksAPI, UserAPI $userAPI, ArtistAPI $artistAPI)
    {
        // $genres = [];
        // $autoTracks = $vibe->tracks()->where('auto_related', 1)->get();
        // $autoTracks = $tracksAPI->load($autoTracks);

        // foreach ($autoTracks as $track) {
        //     $artistGenres = $artistAPI->get($track->artists[0]->id)->genres;
        //     foreach ($artistGenres as $artistGenre) {
        //         if (in_array($artistGenre, $genres)) {
        //             $genres[$artistGenre][] = $track;
        //         } else {
        //             $genres[] = $artistGenre;
        //         }
        //     }
        // }

        // dd($genres);













        if ($vibe->auto_dj) {
            $tracks = $vibe->tracks()->where('auto_related', 1)->get();
        } else {
            $tracks = $vibe->tracks()->where('auto_related', 0)->get();
        }

        $loadedTracks = $tracksAPI->load($tracks);
        return view('vibe.show', [
            'vibe' => $playlist->load($vibe),
            'apiTracks' => $tracksAPI->check($loadedTracks)
        ]);



        // dd($tracks->getRecommendations([
        //     'target_popularity' => 25,
        //     'market' => array('US'),
        //     'seed_genres' => array('rock'),
        //     'limit' => 100
        // ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\vibe  $vibe
     * @return \Illuminate\Http\Response
     */
    public function edit(Vibe $vibe, Playlist $playlist)
    {
        $this->authorize('update', $vibe);
        return view('vibe.edit')->with('vibe', $playlist->load($vibe));
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
        $vibe->update(request(['description', 'open', 'auto_dj']));
        $playlist->update($vibe->api_id, $request->input('name'));
        event(new VibeUpdated($vibe));
        return redirect($vibe->path());
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
        $message = $playlist->load($vibe)->name . ' has been deleted.';
        $playlist->delete($vibe->api_id);
        $vibe->users()->detach();
        $vibe->tracks()->detach();
        $vibe->delete();
        return redirect('/home')->with('message', $message);
    }
}
