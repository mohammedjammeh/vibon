<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\User;
use App\Vibe;
use App\Notifications\ResponseToJoinAVibe;

class HomeTest extends TestCase

{
	use DatabaseMigrations, WithoutMiddleware;


	/** @test **/
    public function a_user_can_see_his_accepted_notifications()
    
    {

    	$this->withoutExceptionHandling();

    	$user = factory(User::class)->create();

    	$vibe = factory(Vibe::class)->create();


    	
        $user->notify(new ResponseToJoinAVibe($vibe->id, 1));



    	$this->actingAs($user)

    		->get(route('home.index'))

    		->assertSuccessful()

    		->assertSee("Your request to join '{$vibe->title}' has been accepted.");

    }

}
