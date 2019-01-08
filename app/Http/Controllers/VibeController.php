<?php

namespace App\Http\Controllers;

use App\Vibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VibeController extends Controller
{
    public function __construct()
    {
        $this->middleware('spotifySession');
        $this->middleware('auth', ['only' => ['create', 'store', 'edit', 'destroy']]);
        $this->middleware('spotifyAuth', ['only' => ['create', 'store', 'edit', 'destroy']]);
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
        $request->validate([
            'title' => ['required', 'min:3', 'max:25'],
            'description' => ['required', 'min:3', 'max:255']
        ]);

        $this->spotifyAPI()->createPlaylist([
            'name' => request('title')
        ]);

        $playlists = $this->spotifyAPI()->getUserPlaylists($this->spotifyAPI()->me()->id);
        $newPlaylistID = current($playlists->items)->id;

        $vibes = Vibe::all();
        $key = mt_rand(1000,9999);
        for ($vibe = 0; $vibe < count($vibes); $vibe++) {
            if ($key == $vibes[$vibe]->key) {
                $key = mt_rand(1000,9999);
                $vibe = 0;
            }
        }

        $vibe = Vibe::create([
            'title' => request('title'),
            'api_id' => $newPlaylistID,
            'description' => request('description'),
            'key' => $key
        ]);

        $vibe->users()->attach(Auth::id(), ['vibe_dj' => 1]);

        $vibeUrl = '/vibe/' . $vibe->id;
        return redirect($vibeUrl);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\vibe  $vibe
     * @return \Illuminate\Http\Response
     */
    public function show(Vibe $vibe)
    {
        $vibe = Vibe::findOrFail($vibe)[0];
        return view('vibe.show')->with('vibe', $vibe);
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

        $vibe = Vibe::findOrFail($vibe)[0];
        return view('vibe.edit')->with('vibe', $vibe);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\vibe  $vibe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vibe $vibe)
    {
        $this->authorize('update', $vibe);

        $request->validate([
            'title' => ['required', 'min:3', 'max:25'],
            'description' => ['required', 'min:3', 'max:255']
        ]);

        $vibe = Vibe::findOrFail($vibe)[0];
        $vibe->title = request('title');
        $vibe->description = request('description');
        $vibe->save();

        $this->spotifyAPI()->updatePlaylist($vibe->api_id, [
            'name' => request('title')
        ]);

        $vibeUrl = '/vibe/' . $vibe->id;
        return redirect($vibeUrl);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\vibe  $vibe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vibe $vibe)
    {
        $this->authorize('update', $vibe);
        
        $this->spotifyAPI()->unfollowPlaylistForCurrentUser($vibe->api_id);

        $vibe = Vibe::findOrFail($vibe)[0];
        $vibe->users()->detach(Auth::id());
        $vibe->delete();

        return redirect('/home');
    }
}
