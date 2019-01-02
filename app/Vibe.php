<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class vibe extends Model
{
    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function tracks() {
        return $this->belongsToMany(Track::class)->withTimestamps();
    }
}
