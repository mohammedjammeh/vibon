<?php

namespace Tests\Unit\Traits;

use App\MusicAPI\Playlist;
use App\Traits\VibeShowTrait;
use App\Vibe;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VibeShowTest extends TestCase
{
    use WithFaker, RefreshDatabase, VibeShowTrait;

    public function test_the_show_response_method_returns_the_loaded_vibe_with_its_updated_attributes_and_a_message()
    {
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $loadedVibe = app(Playlist::class)->load($vibe);
        $message = $loadedVibe->name . ' has been loaded.';

        $vibeShow = $this->showResponse($loadedVibe, $message);

        $this->assertTrue($vibeShow['vibe']->destroyable);
        $this->assertEmpty($vibeShow['vibe']->requests);
        $this->assertTrue($vibeShow['vibe']->currentUserIsAMember);
        $this->assertFalse($vibeShow['vibe']->hasJoinRequestFromUser);
        $this->assertNull($vibeShow['vibe']->joinRequestFromUser);
        $this->assertEquals($vibeShow['message'], $message);
    }

    public function test_the_update_attributes_method_updates_the_attributes_of_a_loaded_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $loadedVibe = app(Playlist::class)->load($vibe);

        $this->updateAttributes($loadedVibe);

        $this->assertTrue($loadedVibe->destroyable);
        $this->assertEmpty($loadedVibe->requests);
        $this->assertTrue($loadedVibe->currentUserIsAMember);
        $this->assertFalse($loadedVibe->hasJoinRequestFromUser);
        $this->assertNull($loadedVibe->joinRequestFromUser);
    }
}
