<?php

namespace Tests\Unit;

use App\Vibe;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VibeTest extends TestCase
{
	use RefreshDatabase;
	
    /** @test */
    public function a_vibe_has_a_path()
    {
        $vibe = factory(Vibe::class)->create();
        $this->assertEquals('/vibe/' . $vibe->id , $vibe->path());
    }
}
