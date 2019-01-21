<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vibe extends Model
{


    protected $guarded = [];



    public function users() 
    
    {

        return $this->belongsToMany(User::class)->withPivot('owner')->withTimestamps();

    }



    public function tracks() 

    {

        return $this->belongsToMany(Track::class)->withTimestamps();

    }



    public function joinRequests() 

    {

    	return $this->hasMany(JoinRequest::class);

    }



    public function privateType()

    {

        if($this->type !== 1) {
            return false;
        } 

        return true;
    }



    public function autoDJ()

    {

        if($this->auto_dj !== 1) {
            return false;
        } 

        return true;

    }

}
