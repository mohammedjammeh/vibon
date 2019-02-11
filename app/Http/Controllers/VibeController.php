<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;

use App\Music\Playlist;
use App\Music\Tracks;
use App\Music\Spotify\WebAPI;

use App\Http\Requests\StoreVibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VibeController extends Controller
{
    public function __construct() {
        parent::__construct();
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
            'name' => request('name'),
            'api_id' => $newPlaylist->id,
            'description' => request('description'),
            'open' => request('open'),
            'auto_dj' => request('auto_dj')
        ]);

        // $vibe->users()->attach(Auth::id(), ['owner' => 1]);
        // return redirect('/vibe/' . $vibe->id);

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
        $vibe->update(request(['name', 'description', 'open', 'auto_dj']));
        $playlist->update($vibe->api_id, $request->input('name'));
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
        $message = $vibe->name . ' has been deleted.';
        $vibe->users()->detach(Auth::id());
        $vibe->delete();
        return redirect('/home')->with('message', $message);
    }
}
