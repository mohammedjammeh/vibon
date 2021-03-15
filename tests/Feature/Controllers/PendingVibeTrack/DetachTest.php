<?php

namespace Tests\Feature\Controllers\PendingVibeTrack;

use App\Events\PendingDetachVibeTrackCreated;
use App\Events\PendingDetachVibeTrackDeleted;
use App\Notifications\PendingDetachVibeTracksAcceptedNotification;
use App\Notifications\PendingDetachVibeTracksRejectedNotification;
use App\PendingVibeTrack;
use App\Track;
use App\Vibe;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DetachTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_that_the_store_method_stores_a_pending_vibe_track_to_detach_and_triggers_the_pending_vibe_track_created_event()
    {
        Event::fake();

        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user);

        $response = $this->post(route('pending-vibe-track-detach.store', [
            'vibe' => $vibe,
            'track' => $track,
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($response->original['vibe']->id, $vibe->id);
        $this->assertDatabaseHas('pending_vibe_tracks', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id,
            'attach' => false,
        ]);
        Event::assertDispatched(PendingDetachVibeTrackCreated::class);
    }

    public function test_that_destroy_method_deletes_a_pending_vibe_track_to_detach_and_triggers_the_pending_vibe_track_deleted_event()
    {
        Event::fake();

        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('detach')->create(['user_id' => $this->user]);

        $response = $this->delete(route('pending-vibe-track-detach.destroy', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($response->original['vibe']->id, $pendingVibeTrack->vibe_id);
        $this->assertDatabaseMissing('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $this->user->id,
            'attach' => $pendingVibeTrack->attach,
        ]);
        Event::assertDispatched(PendingDetachVibeTrackDeleted::class);
    }

    public function test_that_the_respond_method_responds_to_pending_vibe_track_to_detach_and_triggers_the_pending_detach_vibe_tracks_events_to_notify_user()
    {
        Notification::fake();

        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTracks = factory(PendingVibeTrack::class, 2)->states('detach')->create(['vibe_id' => $vibe->id]);
        $acceptPendingVibeTrack = $pendingVibeTracks->first();
        $rejectPendingVibeTrack = $pendingVibeTracks->last();

        $this->attachPendingVibeTracks($vibe, $acceptPendingVibeTrack, $rejectPendingVibeTrack);

        $response = $this->post(route('pending-vibe-track-detach.respond', $vibe), [
            'accepted' => [$acceptPendingVibeTrack->track->id],
            'rejected' => [$rejectPendingVibeTrack->track->id]
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($response->original['vibe']->id, $vibe->id);

        $this->assertDatabaseMissing('track_vibe', [
            'track_id' => $acceptPendingVibeTrack->track->id,
            'vibe_id' => $vibe->id,
            'auto_related' => false
        ]);

        $this->assertDatabaseHas('track_vibe', [
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
            PendingDetachVibeTracksAcceptedNotification::class,
            function ($notification, $channels) use ($acceptPendingVibeTrack) {
                return $notification->vibe_id === $acceptPendingVibeTrack->vibe_id &&
                    $notification->track_id === $acceptPendingVibeTrack->track_id &&
                    boolval($notification->attach) === $acceptPendingVibeTrack->attach;
            }
        );

        Notification::assertSentTo(
            $rejectPendingVibeTrack->user,
            PendingDetachVibeTracksRejectedNotification::class,
            function ($notification, $channels) use ($rejectPendingVibeTrack) {
                return $notification->vibe_id === $rejectPendingVibeTrack->vibe_id &&
                    $notification->track_id === $rejectPendingVibeTrack->track_id &&
                    boolval($notification->attach) === $rejectPendingVibeTrack->attach;
            }
        );
    }
}
