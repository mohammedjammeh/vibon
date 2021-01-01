<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PendingVibeTrack extends Pivot
{
    protected $guarded = [];

    protected $table = 'pending_vibe_tracks';

    protected $casts = [
        'track_id' => 'integer',
        'vibe_id' => 'integer',
        'user_id' => 'integer'
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function vibe()
    {
        return $this->belongsTo(Vibe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
