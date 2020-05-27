<?php

namespace Tests;

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
}
