<?php

namespace Tests\Feature\Controllers;

use App\Events\PendingAttachVibeTrackCreated;
use App\Events\PendingAttachVibeTrackDeleted;
use App\Notifications\PendingVibeTrackAcceptedNotification;
use App\Notifications\PendingVibeTrackRejectedNotification;
use App\PendingVibeTrack;
use App\Track;
use App\Vibe;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttachTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_the_store_method_stores_a_pending_vibe_track_to_attach_and_triggers_the_pending_vibe_track_created_event()
    {
        Event::fake();

        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user);

        $response = $this->post(route('pending-vibe-track-attach.store', [
            'vibe' => $vibe,
            'track-api' => $track->api_id,
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($response->original['vibe']->id, $vibe->id);
        $this->assertDatabaseHas('pending_vibe_tracks', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id,
            'attach' => true,
        ]);
        Event::assertDispatched(PendingAttachVibeTrackCreated::class);
    }

    public function test_destroy_method_deletes_a_pending_vibe_track_to_attach_and_triggers_the_pending_vibe_track_deleted_event()
    {
        Event::fake();

        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['user_id' => $this->user]);

        $response = $this->delete(route('pending-vibe-track-attach.destroy', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($response->original['vibe']->id, $pendingVibeTrack->vibe_id);
        $this->assertDatabaseMissing('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $pendingVibeTrack->user_id,
            'attach' => $pendingVibeTrack->attach
        ]);
        Event::assertDispatched(PendingAttachVibeTrackDeleted::class);
    }

    public function test_the_accept_method_accepts_pending_vibe_track_to_attach_and_triggers_the_pending_vibe_track_accepted_event_to_notify_user()
    {
        Notification::fake();

        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['vibe_id' => $vibe->id]);

        $response = $this->delete(route('pending-vibe-track-attach.accept', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($response->original['vibe']->id, $pendingVibeTrack->vibe_id);
        $this->assertDatabaseHas('track_vibe', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $vibe->id,
            'auto_related' => false
        ]);
        Notification::assertSentTo(
            $pendingVibeTrack->user,
            PendingVibeTrackAcceptedNotification::class,
            function ($notification, $channels) use ($pendingVibeTrack) {
                $notificationAttach = $notification->attach === '0' ? false : true;
                return $notification->vibe_id === $pendingVibeTrack->vibe_id &&
                    $notification->track_id === $pendingVibeTrack->track_id &&
                    $notificationAttach === $pendingVibeTrack->attach;
            }
        );
    }

    public function test_the_reject_method_rejects_pending_vibe_track_to_attach_and_triggers_the_pending_vibe_track_reject_event_to_notify_user()
    {
        Notification::fake();

        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['vibe_id' => $vibe->id]);

        $response = $this->delete(route('pending-vibe-track-attach.reject', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($response->original['vibe']->id, $pendingVibeTrack->vibe_id);
        $this->assertDatabaseMissing('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $pendingVibeTrack->user_id,
            'attach' => $pendingVibeTrack->attach,
        ]);
        $this->assertDatabaseMissing('track_vibe', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->_vibe_id,
            'auto_related' => false
        ]);

        Notification::assertSentTo(
            $pendingVibeTrack->user,
            PendingVibeTrackRejectedNotification::class,
            function ($notification, $channels) use ($pendingVibeTrack) {
                $notificationAttach = $notification->attach === '0' ? false : true;
                return $notification->vibe_id === $pendingVibeTrack->vibe_id &&
                    $notification->track_id === $pendingVibeTrack->track_id &&
                    $notificationAttach === $pendingVibeTrack->attach;
            }
        );
    }
}