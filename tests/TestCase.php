<?php

namespace Tests;

use App\MusicAPI\Tracks;
use App\User;
use App\MusicAPI\InterfaceAPI;
use App\MusicAPI\Fake\WebAPI as FakeAPI;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $user;

    function setUp() 
    {
    	parent::setUp();
    	$this->defaultHeaders = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];
    	$this->user = factory(User::class)->create();
		$this->actingAs($this->user);
		app()->bind(InterfaceAPI::class, FakeAPI::class);
    }

    protected function randomTrackCategory()
    {
        return array_random([
            Tracks::PLAYLIST,
            Tracks::NOT_ON_PLAYLIST,
            Tracks::NOT_ON_VIBON,
            Tracks::PENDING_TO_ATTACH,
            Tracks::PENDING_TO_DETACH
        ]);
    }

    public function attachPendingVibeTracks($vibe, $acceptPendingVibeTrack, $rejectPendingVibeTrack)
    {
        $vibe->tracks()->attach([
            $acceptPendingVibeTrack->id => [
                'auto_related' => false,
                'user_id' => $acceptPendingVibeTrack->user->id,
            ],
            $rejectPendingVibeTrack->id => [
                'auto_related' => false,
                'user_id' => $rejectPendingVibeTrack->user->id,
            ],
        ]);

        $vibe->tracks()->attach($acceptPendingVibeTrack->id, [
            'auto_related' => true,
            'user_id' => $acceptPendingVibeTrack->user->id,
        ]);
    }
}
