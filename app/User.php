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

    public function requestNotifications() 
    {
        return $this->unreadNotifications->where('type', RequestToJoinAVibe::class);
    }

    public function isAuthorisedWith($api) 
    {
        if($this->where('api', $api)->get()) {
            return true;
        }
        return false;
    }
}
