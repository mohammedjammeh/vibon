<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\VibeCreated;
use App\AutoDJ\Genre as AutoGenre;

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
        return $this->belongsToMany(Track::class)->withPivot('auto_related')->withTimestamps();
    }

    public function showTracks() 
    {
        if ($this->auto_dj) {
            return AutoGenre::orderTracksByPopularity($this);
        }
        return $this->tracks()->where('auto_related', false)->get();
    }
    
    public function path() 
    {
        return route('vibe.show', $this);
    }

    public function joinRequests() 
    {
    	return $this->hasMany(JoinRequest::class)->with('user');
    }

    public function hasMember($id) 
    {
        return $this->users->where('id', $id)->first();
    }

    public function hasJoinRequestFrom($id) 
    {
        return $this->joinRequests->where('user_id', $id)->first();
    }

    public function owner()
    {
        return $this->users()->where('owner', 1)->first();
    }

    public function ownerNotificationFrom($id)
    {
        return $this->owner()
            ->unreadNotifications
            ->where('data.requester_id', $id)
            ->where('data.vibe_id', $this->id)
            ->last();
    }
}
