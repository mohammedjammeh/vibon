<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
	protected $guarded = [];
	
    public function vibes() {
        return $this->belongsToMany(Vibe::class)->withTimestamps();
    }
}
