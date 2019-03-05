<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
	protected $guarded = [];
	
    public function vibes() 
    {
        return $this->belongsToMany(Vibe::class)->withPivot('auto_related')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_track')->withPivot('type')->withTimestamps();
    }

    public function find($apiId)
    {
    	return $this->where('api_id', $apiId)->first();
    }
}
