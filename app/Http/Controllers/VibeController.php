<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\MusicAPI\Playlist;
use App\MusicAPI\Tracks as TracksAPI;
use App\Events\VibeCreated;
use App\Events\VibeUpdated;
use App\Http\Requests\StoreVibe;

class VibeController extends Controller
{
    public function __construct()
    {
        $this->middleware('setAccessToken');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\MusicAPI\Playlist  $playlist
     * @return array
     */
    public function index(Playlist $playlist)
    {
        $vibes = $playlist->loadMany(Vibe::all());
        dd($vibes);
        return compact('vibes');
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
     * @param \App\Http\Requests\StoreVibe  $request
     * @param \App\MusicAPI\Playlist  $playlist
     * @return  array
     */
    public function store(StoreVibe $request, Playlist $playlist)
    {
        $newPlaylist = $playlist->create(
            $request->input('name'),
            $request->input('description')
        );

        $vibe = Vibe::create([
            'api_id' => $newPlaylist->id,
            'open' => $request->input('open'),
            'auto_dj' => $request->input('auto_dj')
        ]);
        $vibe->users()->attach(Auth()->user()->id, ['owner' => true]);
        event(new VibeCreated($vibe));
        return ['message' => $request->input('name') . ' has been created.'];
    }

    /**
     * Display the specified resource.
     *
     * @param \App\vibe  $vibe
     * @param \App\MusicAPI\Playlist  $playlist
     * @param \App\MusicAPI\Tracks  $tracksAPI
     * @return \Illuminate\Http\Response
     */
    public function show(Vibe $vibe, Playlist $playlist, TracksAPI $tracksAPI)
    {
//        this needs to be updated if it is to be used as the loadFor method now has the functionality which checks the tracks..
        $loadedTracks = $tracksAPI->loadFor($vibe);
        return view('vibe.show', [
            'vibe' => $playlist->load($vibe),
            'apiTracks' => $tracksAPI->check($loadedTracks)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\vibe  $vibe
     * @param \App\MusicAPI\Playlist  $playlist
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
     * @param  \App\Http\Requests\StoreVibe  $request
     * @param  \App\vibe  $vibe
     * @param  \App\MusicAPI\Playlist  $playlist
     * @return \Illuminate\Http\Response
     */
    public function update(StoreVibe $request, Vibe $vibe, Playlist $playlist)
    {
        $this->authorize('update', $vibe);
        $vibe->update(request(['open', 'auto_dj']));
        $playlist->update($vibe->api_id, $request->input('name'), $request->input('description'));
        event(new VibeUpdated($vibe));
        return redirect($vibe->path);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\vibe  $vibe
     * @param  \App\MusicAPI\Playlist  $playlist
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
        return redirect(route('index'))->with('message', $message);
    }
}
