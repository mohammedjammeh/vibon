<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
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

        $key = mt_rand(1000,9999) ;

        $vibeFound = Vibe::where('key', $key)->get();

        while(!$vibeFound->isEmpty()) {
            $key = mt_rand(1000,9999);
            $vibeFound = Vibe::where('key', $key)->get();
        }

        $vibe = Vibe::create([
            'title' => request('title'),
            'api_id' => $newPlaylistID,
            'description' => request('description'),
            'key' => $key
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
    public function show(Vibe $vibe)
    {
        $vibeFound = Vibe::findOrFail($vibe->id);
        $vibeFoundTracks = $vibeFound->tracks()->get();
        $vibeFoundMembers = $vibeFound->users()->orderBy('user_vibe.created_at', 'asc')->get();

        $tracks = array();
        for ($track=0; $track < count($vibeFoundTracks); $track++) { 
            $tracks[] = $this->spotifyAPI()->getTrack($vibeFoundTracks[$track]->api_id);
        }

        $user = User::find(Auth::id());
        $userVibesTracks = $user::with('vibes.tracks')->where('id', Auth::id())->get();

        $vibeOwner = $vibe->users()->where('owner', 1)->first();
        $joinRequest = $vibeOwner->notifications->where('data.requester_id', Auth::id())->where('data.vibe_id', $vibe->id)->first();


        $unacceptedRequests = $vibeOwner->unreadNotifications->where('data.accepted', 0);
        $unacceptedUsers = array();
        for ($unacceptedRequest=0; $unacceptedRequest < count($unacceptedRequests); $unacceptedRequest++) { 
            $unacceptedUsers[] = User::findOrFail($unacceptedRequests[$unacceptedRequest]->data['requester_id']);
        }

        $showContent = array('vibe' => $vibeFound, 'tracks' => $tracks, 'user' => $userVibesTracks, 'members' => $vibeFoundMembers, 'joinRequest' => $joinRequest, 'unacceptedUsers' => $unacceptedUsers);
        return view('vibe.show')->with('showContent', $showContent);

        //for track suggestions, randomly get them from:
        // 1. user's library
        // 2. user's playlists
        // 3. songs related to the ones currently playing
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

        $vibeFound = Vibe::findOrFail($vibe->id);
        return view('vibe.edit')->with('vibe', $vibeFound);
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

        $vibeFound = Vibe::findOrFail($vibe->id);
        $vibeFound->title = request('title');
        $vibeFound->description = request('description');
        $vibeFound->save();

        $this->spotifyAPI()->updatePlaylist($vibeFound->api_id, [
            'name' => request('title')
        ]);

        return redirect('/vibe/' . $vibeFound->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\vibe  $vibe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vibe $vibe)
    {
        $this->authorize('delete', $vibe);
        
        $this->spotifyAPI()->unfollowPlaylistForCurrentUser($vibe->api_id);

        $vibeFound = Vibe::findOrFail($vibe->id);
        $message = $vibeFound->title . ' has been deleted.';
        $vibeFound->users()->detach(Auth::id());
        $vibeFound->delete();

        return redirect('/home')->with('message', $message);
    }
}
