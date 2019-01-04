<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vibe extends Model
{
    protected $guarded = [];

    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function tracks() {
        return $this->belongsToMany(Track::class)->withTimestamps();
    }
}
