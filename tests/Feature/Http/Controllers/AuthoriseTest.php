<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthoriseTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_user_trying_to_be_authorised_with_spotify_is_redirected_to_the_spotify_authorisation_url()
    {
        $this->get(route('authorise'))
            ->assertRedirect('https://accounts.spotify.com/authorize?client_id=123');
    }
}
