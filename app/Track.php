<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
	protected $guarded = [];
	
    public function vibes() 
    {
        return $this->belongsToMany(Vibe::class)
            ->withPivot('auto_related')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_track')
            ->withPivot('type')
            ->withTimestamps();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'track_genre')
            ->withTimestamps();
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function scopeAutoRelatedTo($query, $vibe) 
    {
        return $query->whereHas('vibes', function($vibeQuery) use($vibe) {
            return $vibeQuery->where('id', $vibe->id)->where('track_vibe.auto_related', 1);
        });
    }

    public function scopeBelongsToMembersOf($query, $vibe)
    {
        return $query->whereHas('users', function($userQuery) use($vibe) {
            return $userQuery->isMemberOf($vibe);
        });
    }

    public function votesCountOn($vibe)
    {
        return $this->votes->where('vibe_id', $vibe->id)->count();
    }

    public function isVotedByAuthUserOn($vibe)
    {
        $votes = $this->votes
            ->where('vibe_id', $vibe->id)
            ->where('user_id', auth()->user()->id);

        if ($votes->isEmpty()) {
            return false;
        }
        return true;
    }
}
