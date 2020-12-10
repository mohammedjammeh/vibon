<?php

namespace Tests\Feature\Controllers;

use App\User;
use App\Vibe;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_vibes_method_returns_current_users_vibes_ids()
    {
        $user = factory(User::class)->create();
        $autoVibe = factory(Vibe::class)->create(['auto_dj' => true]);
        $manualVibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $user->vibes()->attach([$autoVibe->id, $manualVibe->id]);

        $this->actingAs($user);
        $response = $this->get(route('user.vibes'))->baseResponse->original;

        $this->assertEquals($autoVibe->id, $response['auto']->first());
        $this->assertEquals($manualVibe->id, $response['manual']->first());
    }

    public function test_attributes_method_returns_user()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->get(route('user.attributes'))->baseResponse->original;

        $this->assertEquals($user->attributes, $response->attributes);
    }
}
