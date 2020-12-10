<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\RequestToJoinAVibe;

class User extends Authenticatable
{
    use Notifiable;

    public const SPOTIFY = 1;
    public const APPLE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function vibes() 
    {
        return $this->belongsToMany(Vibe::class)->withPivot('owner')->withTimestamps();
    }

    public function tracks() 
    {
        return $this->belongsToMany(Track::class, 'user_track')->withPivot('type')->withTimestamps();
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function notificationsFor($vibe)
    {
       return $this->notifications
           ->where('data.vibe_id', $vibe->id)
           ->sortByDesc('created_at');
    }

    public function isAuthorisedWith($api) 
    {
        if($this->api == $api) {
            return true;
        }
        return false;
    }

    public function scopeIsMemberOf($query, $vibe)
    {
        return $query->whereHas('vibes', function($vibeQuery) use($vibe) {
            return $vibeQuery->where('user_vibe.vibe_id', $vibe->id);
        });
    }
}
