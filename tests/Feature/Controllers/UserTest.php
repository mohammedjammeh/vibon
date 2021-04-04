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

    public function test_attributes_method_returns_expected_user_data()
    {
        $user = factory(User::class)->create();
        $autoVibe = factory(Vibe::class)->create(['auto_dj' => true]);
        $manualVibe = factory(Vibe::class)->create(['auto_dj' => false]);
        $user->vibes()->attach([$autoVibe->id, $manualVibe->id]);

        $this->actingAs($user);
        $response = $this->get(route('user.attributes'))->baseResponse->original;

        $this->assertEquals($user->id, $response['id']);
        $this->assertEquals($user->access_token, $response['access_token']);
        $this->assertEquals($user->token_set_at, $response['token_set_at']);
        $this->assertContains($autoVibe->id, $response['auto_vibes']);
        $this->assertContains($manualVibe->id, $response['manual_vibes']);
        $this->assertArrayHasKey('device_id', $response);
    }
}
