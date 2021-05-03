<?php

namespace App;

use App\Notifications\JoinedVibe;
use App\Notifications\LeftVibe;
use App\Notifications\PendingAttachVibeTracksAcceptedNotification;
use App\Notifications\PendingAttachVibeTracksRejectedNotification;
use App\Notifications\PendingDetachVibeTracksAcceptedNotification;
use App\Notifications\PendingDetachVibeTracksRejectedNotification;
use App\Notifications\RequestToJoinAVibe;
use App\Traits\NotificationShowTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\AutoDJ\Genre as AutoGenre;

class Vibe extends Model
{
    use NotificationShowTrait;

    protected $guarded = [];

    protected $casts = [
        'open' => 'boolean',
        'auto_dj' => 'boolean'
    ];

    public function users() 
    {
        return $this->belongsToMany(User::class)
            ->withPivot('owner')
            ->withTimestamps()
            ->orderBy('user_vibe.created_at', 'asc'); 
    }

    public function tracks() 
    {
        return $this->belongsToMany(Track::class)->withPivot(['id', 'auto_related'])->withTimestamps();
    }

    public function joinRequests()
    {
        return $this->hasMany(JoinRequest::class)->with('user');
    }

    public function pendingTracks()
    {
        return $this->hasMany(PendingVibeTrack::class)->with('track');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function hasMember($user)
    {
        if($this->users->where('id', $user->id)->first()) {
            return true;
        }
        return false;
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
        return $this->users()->where('owner', true)->first();
    }

    public function getPathAttribute() 
    {
        return route('vibe.show', $this);
    }

    public function getShowTracksAttribute()
    {
        if ($this->auto_dj) {
            return AutoGenre::orderTracksByPopularity($this);
        }

        return $this->tracks()
            ->where('auto_related', false)
            ->withCount(['votes' => function (Builder $query) {
                $query->where('vibe_id', $this->id);
            }])
            ->orderBy('votes_count', 'desc')
            ->orderBy('pivot_created_at', 'asc')
            ->orderBy('pivot_id', 'asc')
            ->get();
    }

    public function getPendingTracksToAttachAttribute()
    {
        return $this->pendingTracks->where('attach', true);
    }

    public function getPendingTracksToDetachAttribute()
    {
        return $this->pendingTracks->where('attach', false);
    }

    public function notifications()
    {
        $notifications =  auth()->user()->notificationsFor($this);
        foreach ($notifications as $notification) {
            if ($this->isUserNotification($notification)) {
                $notification->data = $this->addUserData($notification);
            }

            if($this->isTrackNotification($notification)) {
                $notification->data = $this->addTrackData($notification);
            }
        }

        return $notifications;
    }

    protected function isUserNotification($notification)
    {
        return $notification->type === RequestToJoinAVibe::class ||
            $notification->type === LeftVibe::class ||
            $notification->type === JoinedVibe::class;
    }

    protected function isTrackNotification($notification)
    {
        return $notification->type === PendingAttachVibeTracksAcceptedNotification::class ||
            $notification->type === PendingAttachVibeTracksRejectedNotification::class ||
            $notification->type === PendingDetachVibeTracksAcceptedNotification::class ||
            $notification->type === PendingDetachVibeTracksRejectedNotification::class;
    }
}
