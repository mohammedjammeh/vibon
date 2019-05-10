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

    public function joinRequests() 
    {
        return $this->hasMany(JoinRequest::class)->with('user');
    }

    public function showTracks() 
    {
        if ($this->auto_dj) {
            return AutoGenre::orderTracksByPopularity($this);
        }
        return $this->tracks()->where('auto_related', false)->get();
    }

    public function hasMember($user) 
    {
        return $this->users->where('id', $user->id)->first();
    }

    public function hasJoinRequestFrom($user) 
    {
        if ($this->joinRequestFrom($user)) {
            return true;
        }
        return false;
    }

    public function joinRequestFrom($user)
    {
        return $this->joinRequests->where('user_id', $user->id)->first();
    }

    public function getOwnerAttribute()
    {
        return $this->users()->where('owner', 1)->first();
    }

    public function getPathAttribute() 
    {
        return route('vibe.show', $this);
    }
}
