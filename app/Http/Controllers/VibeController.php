<?php

namespace App\Http\Controllers;

use App\Traits\VibeShowTrait;
use App\Vibe;
use App\MusicAPI\Playlist;
use App\MusicAPI\Tracks as TracksAPI;
use App\Events\VibeCreated;
use App\Events\VibeUpdated;
use App\Http\Requests\StoreVibe;

class VibeController extends Controller
{
    use VibeShowTrait;

    public function __construct()
    {
        $this->middleware('authenticated');
    }

    public function index(Playlist $playlist)
    {
        return Vibe::all()->map(function($vibe) use($playlist) {
            $loadedVibe = app(Playlist::class)->load($vibe);
            return $this->showResponse($loadedVibe);
        });
    }

    public function create()
    {
//        return view('vibe.create');
    }

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

        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }

    public function show(Vibe $vibe, Playlist $playlist, TracksAPI $tracksAPI)
    {
//        this needs to be updated if it is to be used as the loadFor method now has the functionality which checks the tracks..
//        $loadedTracks = $tracksAPI->loadFor($vibe);
//        return view('vibe.show', [
//            'vibe' => $playlist->load($vibe),
//            'apiTracks' => $tracksAPI->check($loadedTracks)
//        ]);
    }

    public function edit(Vibe $vibe, Playlist $playlist)
    {
//        $this->authorize('update', $vibe);
//        return view('vibe.edit')->with('vibe', $playlist->load($vibe));
    }

    public function update(StoreVibe $request, Vibe $vibe, Playlist $playlist)
    {
        $this->authorize('delete', $vibe);
        $vibe->update(request(['open', 'auto_dj']));
        $playlist->update($vibe->api_id, $request->input('name'), $request->input('description'));
        event(new VibeUpdated($vibe));

        $loadedVibe = app(Playlist::class)->load($vibe);
        return $this->showResponse($loadedVibe);
    }

    public function destroy(Vibe $vibe, Playlist $playlist)
    {
        $this->authorize('delete', $vibe);
        $message = $playlist->load($vibe)->name . ' has been deleted.';
        $playlist->delete($vibe->api_id);
        $vibe->users()->detach();
        $vibe->tracks()->detach();
        $vibe->delete();
        return ['message' => $message];
    }
}
