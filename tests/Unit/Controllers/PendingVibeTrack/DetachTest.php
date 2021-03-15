<?php

namespace Tests\Unit\Controllers\PendingVibeTrack;

use App\Events\PendingDetachVibeTrackCreated;
use App\Events\PendingDetachVibeTrackDeleted;
use App\Events\PendingDetachVibeTracksRespondedTo;
use App\Notifications\PendingAttachVibeTracksAcceptedNotification;
use App\Notifications\PendingDetachVibeTracksAcceptedNotification;
use App\Notifications\PendingDetachVibeTracksRejectedNotification;
use App\PendingVibeTrack;
use App\Track;
use App\User;
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

    public function test_that_pending_vibe_track_to_detach_can_be_created_by_member_of_a_vibe()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user);

        $response = $this->post(route('pending-vibe-track-detach.store', [
            'vibe' => $vibe,
            'track' => $track,
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('pending_vibe_tracks', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id,
            'attach' => false,
        ]);
    }

    public function test_that_pending_vibe_track_to_detach_cannot_be_created_by_non_member_of_a_vibe()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();

        $response = $this->post(route('pending-vibe-track-detach.store', [
            'vibe' => $vibe,
            'track' => $track,
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('pending_vibe_tracks', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id,
            'attach' => false,
        ]);
    }

    public function test_creating_a_pending_vibe_track_to_detach_triggers_the_pending_vibe_track_created_event()
    {
        Event::fake();

        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user);

        $this->post(route('pending-vibe-track-detach.store', [
            'vibe' => $vibe,
            'track' => $track,
        ]));

        Event::assertDispatched(PendingDetachVibeTrackCreated::class);
    }

    public function test_that_pending_vibe_track_to_detach_can_be_deleted_by_user_who_created_it()
    {
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('detach')->create(['user_id' => $this->user]);

        $response = $this->delete(route('pending-vibe-track-detach.destroy', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $this->user->id,
            'attach' => $pendingVibeTrack->attach,
        ]);
    }

    public function test_that_pending_vibe_track_to_detach_can_be_deleted_by_vibe_owner()
    {
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('detach')->create();
        $pendingVibeTrack->vibe->users()->attach($this->user->id, ['owner' => true]);

        $response = $this->delete(route('pending-vibe-track-detach.destroy', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $this->user->id,
            'attach' => $pendingVibeTrack->attach,
        ]);
    }

    public function test_that_pending_vibe_track_to_detach_cannot_be_deleted_by_user_who_didnt_create_it()
    {
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('detach')->create();
        $pendingVibeTrack->vibe->users()->attach(
            factory(User::class)->create()->id,
            ['owner' => true]
        );

        $response = $this->delete(route('pending-vibe-track-detach.destroy', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $pendingVibeTrack->user_id,
            'attach' => $pendingVibeTrack->attach,
        ]);
    }

    public function test_deleting_a_pending_vibe_track_to_detach_triggers_the_pending_vibe_track_deleted_event()
    {
        Event::fake();

        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('detach')->create(['user_id' => $this->user]);
        $this->delete(route('pending-vibe-track-detach.destroy', $pendingVibeTrack));

        Event::assertDispatched(PendingDetachVibeTrackDeleted::class);
    }







    public function test_that_pending_vibe_track_to_detach_can_be_responded_to_by_owner_of_vibe()
    {
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
    }

    public function test_that_pending_vibe_track_to_detach_cannot_be_responded_to_by_member_of_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $vibe->users()->attach($this->user->id, ['owner' => false]);
        $pendingVibeTracks = factory(PendingVibeTrack::class, 2)->states('detach')->create(['vibe_id' => $vibe->id]);
        $acceptPendingVibeTrack = $pendingVibeTracks->first();
        $rejectPendingVibeTrack = $pendingVibeTracks->last();

        $this->attachPendingVibeTracks($vibe, $acceptPendingVibeTrack, $rejectPendingVibeTrack);

        $response = $this->post(route('pending-vibe-track-detach.respond', $vibe), [
            'accepted' => [$acceptPendingVibeTrack->track->id],
            'rejected' => [$rejectPendingVibeTrack->track->id]
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_that_responding_to_pending_vibe_track_to_detach_stores_and_leaves_correct_track_data_based_on_response()
    {
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTracks = factory(PendingVibeTrack::class, 2)->states('detach')->create(['vibe_id' => $vibe->id]);
        $acceptPendingVibeTrack = $pendingVibeTracks->first();
        $rejectPendingVibeTrack = $pendingVibeTracks->last();

        $this->attachPendingVibeTracks($vibe, $acceptPendingVibeTrack, $rejectPendingVibeTrack);

        $this->post(route('pending-vibe-track-detach.respond', $vibe), [
            'accepted' => [$acceptPendingVibeTrack->track->id],
            'rejected' => [$rejectPendingVibeTrack->track->id]
        ]);

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

        $this->assertDatabaseHas('track_vibe', [
            'track_id' => $acceptPendingVibeTrack->track->id,
            'vibe_id' => $vibe->id,
            'auto_related' => true
        ]);
    }

    public function test_that_responding_to_pending_vibe_tracks_to_detach_triggers_the_right_event()
    {
        Event::fake();

        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTracks = factory(PendingVibeTrack::class, 2)->states('detach')->create(['vibe_id' => $vibe->id]);
        $acceptPendingVibeTrack = $pendingVibeTracks->first();
        $rejectPendingVibeTrack = $pendingVibeTracks->last();

        $this->attachPendingVibeTracks($vibe, $acceptPendingVibeTrack, $rejectPendingVibeTrack);

        $this->post(route('pending-vibe-track-detach.respond', $vibe), [
            'accepted' => [$acceptPendingVibeTrack->track->id],
            'rejected' => [$rejectPendingVibeTrack->track->id]
        ]);

        Event::assertDispatched(PendingDetachVibeTracksRespondedTo::class);
    }

    public function test_that_responding_to_pending_vibe_tracks_to_detach_deletes_pending_detach_tracks_that_have_been_responded_to()
    {
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

        foreach ($pendingVibeTracks as $pendingVibeTrack) {
            $this->assertDatabaseMissing('pending_vibe_tracks', [
                'track_id' => $pendingVibeTrack->track_id,
                'vibe_id' => $pendingVibeTrack->vibe_id,
                'user_id' => $pendingVibeTrack->user_id,
                'attach' => $pendingVibeTrack->attach,
            ]);
        }
    }

    public function test_that_responding_to_pending_vibe_tracks_to_detach_notifies_users_who_sent_requests_to_remove_tracks()
    {
        Notification::fake();

        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTracks = factory(PendingVibeTrack::class, 2)->states('detach')->create(['vibe_id' => $vibe->id]);
        $acceptPendingVibeTrack = $pendingVibeTracks->first();
        $rejectPendingVibeTrack = $pendingVibeTracks->last();

        $this->attachPendingVibeTracks($vibe, $acceptPendingVibeTrack, $rejectPendingVibeTrack);

        $this->post(route('pending-vibe-track-detach.respond', $vibe), [
            'accepted' => [$acceptPendingVibeTrack->track->id],
            'rejected' => [$rejectPendingVibeTrack->track->id]
        ]);

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
