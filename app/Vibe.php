<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vibe extends Model

{

    protected $guarded = [];



    public function users() 
    
    {

        return $this->belongsToMany(User::class)
            
            ->withPivot('owner')

            ->withTimestamps()

            ->orderBy('user_vibe.created_at', 'asc'); 

    }




    public function tracks() 

    {

        return $this->belongsToMany(Track::class)->withTimestamps();

    }





    public function joinRequests() 

    {

    	return $this->hasMany(JoinRequest::class)->with('user');

    }





    public function hasMember($user) 

    {

        return $this->users->where('id', $user)->first();

    }




    public function hasJoinRequestFrom($user) 

    {

        return $this->joinRequests->where('user_id', $user)->first();

    }



    public function owner()

    {

        return $this->users()->where('owner', 1)->first();

    }



    public function notificationFrom($user)

    {

        return $this->owner()

            ->unreadNotifications

            ->where('data.requester_id', $user)

            ->where('data.vibe_id', $this->id)

            ->last();

    }


}
