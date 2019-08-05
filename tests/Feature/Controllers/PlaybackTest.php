<?php

namespace Tests\Feature\Controllers;

use App\Vibe;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaybackTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_user_can_start_or_resume_playback()
    {
        $vibe = factory(Vibe::class)->create();
        $this->put(route('playback.play', ['vibe' => $vibe]));
    }

    public function test_user_can_pause_playback()
    {

    }


    public function test_user_can_skip_to_next_track_on_playback()
    {

    }

    public function test_user_can_skip_to_previous_track_on_playback()
    {

    }
}
