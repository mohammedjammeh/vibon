<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinRequest extends Model
{
    protected $guarded = [];

    public function user() 
    {
    	return $this->belongsTo(User::class);
    }

    public function vibe()
    {
        return $this->belongsTo(Vibe::class);
    }
}
