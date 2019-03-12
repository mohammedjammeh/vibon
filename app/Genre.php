<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $guarded = [];

    public function tracks()
    {
    	return $this->belongsToMany(Track::class, 'track_genre')->withTimestamps();
    }

    public function scopeWithTracksAutoRelated($query, $vibe) {
    	return $query->with(['tracks' => function($query) use($vibe) {
			return $query->autoRelatedTo($vibe);
	    }])->withCount(['tracks' => function($query) use($vibe) {
			return $query->autoRelatedTo($vibe);
	    }]);
    }

    public function scopeOrderByPopularity($query, $vibe) {
    	return $query->withTracksAutoRelated($vibe)->orderBy('tracks_count', 'DESC');
    }
}
