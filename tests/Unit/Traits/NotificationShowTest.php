<?php

namespace Tests\Unit\Traits;

use App\Traits\NotificationShowTrait;
use App\User;
use App\Vibe;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationShowTest extends TestCase
{
    use WithFaker, RefreshDatabase, NotificationShowTrait;

    public function test_update_data_method_adds_user_display_name_to_notification_data()
    {
        $vibe = factory(Vibe::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->post(route('join-request.store', $vibe));

        $notificationData = $this->updateData($owner->notifications->first());
        $this->assertEquals($notificationData['user_display_name'], $this->user->display_name);
    }
}
