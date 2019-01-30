<?php

namespace App\Http\Controllers;

use App\Vibe;
use App\User;
use App\JoinRequest;
use App\Notifications\RequestToJoinAVibe;
use App\Notifications\ResponseToJoinAVibe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JoinRequestController extends Controller

{


    public function vibeOwner($vibe)

    {

    	return $vibe->users()->where('owner', 1)->first();

    }







    public function join(Vibe $vibe) 

    {   

        $joinRequest = JoinRequest::create([

            'vibe_id' => $vibe->id,

            'user_id' => Auth::user()->id

        ]);


        $this->vibeOwner($vibe)->notify(new RequestToJoinAVibe(Auth::user()->id, $vibe->id));

        return redirect('vibe/' . $vibe->id)->with('message', 'Your request has been sent.');


    }






    public function cancel(Vibe $vibe) 

    {

        $vibe->JoinRequests()

            ->where('vibe_id', $vibe->id)

            ->where('user_id', Auth::user()->id)

            ->orderBy('created_at', 'desc')

            ->first()

            ->delete();


    	$this->vibeOwner($vibe)

            ->notifications

            ->where('data.vibe_id', $vibe->id)

            ->where('data.requester_id', Auth::id())

            ->last()

            ->delete();


    	return redirect('vibe/' . $vibe->id)->with('message', 'Your request to join has been cancelled.');

    }







    public function respond(Request $request, Vibe $vibe, User $user) 

    {

    	$notification = $this->vibeOwner($vibe)

            ->unreadNotifications

            ->where('data.requester_id', $user->id)

            ->where('data.vibe_id', $vibe->id)

            ->first();


    	$notification->markAsRead();


        $vibe->JoinRequests()

            ->where('vibe_id', $vibe->id)

            ->where('user_id', $user->id)

            ->orderBy('created_at', 'desc')

            ->first()

            ->delete();




    	if($request->input('reject')) {

            $user->notify(new ResponseToJoinAVibe($vibe->id, 0));

    		return redirect('/vibe/' . $vibe->id);

    	}



        $user->notify(new ResponseToJoinAVibe($vibe->id, 1));

        $vibe->users()->attach($user->id, ['owner' => 0]);

    	return redirect('/vibe/' . $vibe->id)->with('message', $user->name . ' is now part of this vibe.');

    }


}
