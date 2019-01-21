<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
use App\joinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreVibe;

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
    public function store(StoreVibe $request)

    {

        $this->spotifyAPI()->createPlaylist([

            'name' => request('title')

        ]);


        $playlists = $this->spotifyAPI()->getUserPlaylists($this->spotifyAPI()->me()->id);

        $newPlaylistID = current($playlists->items)->id;


        $vibe = Vibe::create([

            'title' => request('title'),

            'api_id' => $newPlaylistID,

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
    public function show(Vibe $vibe)
    {

        $vibeTracks = $vibe->tracks()->get();

        $vibeMembers = $vibe->users()->orderBy('user_vibe.created_at', 'asc')->get();

        $isUserAMember = $vibe->users()->where('id', Auth::user()->id)->first();





        $userVibesTracks = User::find(Auth::id())::with('vibes.tracks')->where('id', Auth::id())->first();
 
        $userJoinRequest = $vibe->joinRequests()->where('user_id', Auth::user()->id)->first();






        $pendingRequests = $vibe->joinRequests()->get();

        
        $pendingRequesters = array();

        for ($pendingRequest=0; $pendingRequest < count($pendingRequests); $pendingRequest++) { 

            $pendingRequesters[] = User::findOrFail($pendingRequests[$pendingRequest]->user_id);

        }



        $tracks = array();

        for ($track=0; $track < count($vibeTracks); $track++) { 

            $tracks[] = $this->spotifyAPI()->getTrack($vibeTracks[$track]->api_id);

            // $tracks[$track]->vibon_id = $vibeTracks[$track]->id;

        }




        foreach ($tracks as $track) {

            $track->belongs_to_user_vibes = array();


            foreach ($userVibesTracks['vibes'] as $userVibe) {
                
                for ($i=0; $i < count($userVibe->tracks); $i++) { 

                    if($track->id == $userVibe->tracks[$i]->api_id) {

                        $track->belongs_to_user_vibes[] = $userVibe->id;

                        $track->vibon_id = $userVibe->tracks[$i]->id;

                    } 

                }

            }

        }



        return view('vibe.show', [

            'vibe' => $vibe, 

            'tracks' => $tracks, 

            'userVibesTracks' => $userVibesTracks, 

            'members' => $vibeMembers, 

            'isUserAMember' => $isUserAMember,

            'userJoinRequest' => $userJoinRequest, 

            'pendingRequesters' => $pendingRequesters

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

        $this->authorize('update', $vibe);;

        return view('vibe.edit')->with('vibe', $vibe);

    }






    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\vibe  $vibe
     * @return \Illuminate\Http\Response
     */
    public function update(StoreVibe $request, Vibe $vibe)

    {

        $this->authorize('update', $vibe);



        $vibe->title = request('title');

        $vibe->type = request('type');

        $vibe->auto_dj = request('auto_dj');

        $vibe->description = request('description');

        $vibe->save();



        $this->spotifyAPI()->updatePlaylist($vibe->api_id, [

            'name' => request('title')

        ]);


        return redirect('/vibe/' . $vibe->id);
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

        $message = $vibe->title . ' has been deleted.';

        $vibe->users()->detach(Auth::id());

        $vibe->delete();



        return redirect('/home')->with('message', $message);
    }
}
