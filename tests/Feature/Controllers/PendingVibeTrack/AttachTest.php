<?php

namespace Tests\Feature\Controllers;

use App\Events\PendingAttachVibeTrackCreated;
use App\Events\PendingAttachVibeTrackDeleted;
use App\Notifications\PendingAttachVibeTracksAcceptedNotification;
use App\Notifications\PendingAttachVibeTrackRejectedNotification;
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

    public function test_that_the_store_method_stores_a_pending_vibe_track_to_attach_and_triggers_the_pending_vibe_track_created_event()
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

    public function test_that_the_respond_method_responds_to_pending_vibe_tracks_to_attach_and_triggers_the_pending_attach_vibe_tracks_events_to_notify_users()
    {
        Notification::fake();

        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTracks = factory(PendingVibeTrack::class, 2)->states('attach')->create(['vibe_id' => $vibe->id]);
        $acceptPendingVibeTrack = $pendingVibeTracks->first();
        $rejectPendingVibeTrack = $pendingVibeTracks->last();

        $response = $this->post(route('pending-vibe-track-attach.respond', $vibe), [
            'accepted' => [$acceptPendingVibeTrack->track->id],
            'rejected' => [$rejectPendingVibeTrack->track->id]
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($response->original['vibe']->id, $vibe->id);

        $this->assertDatabaseHas('track_vibe', [
            'track_id' => $acceptPendingVibeTrack->track->id,
            'vibe_id' => $vibe->id,
            'auto_related' => false
        ]);

        $this->assertDatabaseMissing('track_vibe', [
            'track_id' => $rejectPendingVibeTrack->track->id,
            'vibe_id' => $vibe->id,
            'auto_related' => false
        ]);

        foreach ($pendingVibeTracks as $pendingVibeTrack) {
            $this->assertDatabaseMissing('pending_vibe_tracks', [
                'track_id' => $pendingVibeTrack->track_id,
                'vibe_id' => $pendingVibeTrack->vibe_id,
                'user_id' => $pendingVibeTrack->user_id,
                'attach' => $pendingVibeTrack->attach,
            ]);
        }

        Notification::assertSentTo(
            $acceptPendingVibeTrack->user,
            PendingAttachVibeTracksAcceptedNotification::class,
            function ($notification, $channels) use ($acceptPendingVibeTrack) {
                return $notification->vibe_id === $acceptPendingVibeTrack->vibe_id &&
                    $notification->track_id === $acceptPendingVibeTrack->track_id &&
                    boolval($notification->attach) === $acceptPendingVibeTrack->attach;
            }
        );

        Notification::assertSentTo(
            $rejectPendingVibeTrack->user,
            PendingAttachVibeTrackRejectedNotification::class,
            function ($notification, $channels) use ($rejectPendingVibeTrack) {
                return $notification->vibe_id === $rejectPendingVibeTrack->vibe_id &&
                    $notification->track_id === $rejectPendingVibeTrack->track_id &&
                    boolval($notification->attach) === $rejectPendingVibeTrack->attach;
            }
        );
    }
}
