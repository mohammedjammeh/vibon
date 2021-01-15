<?php

namespace Tests\Unit\Controllers\PendingVibeTrack;

use App\Events\PendingVibeTrackAccepted;
use App\Events\PendingVibeTrackCreated;
use App\Events\PendingVibeTrackDeleted;
use App\Events\PendingVibeTrackRejected;
use App\Notifications\PendingVibeTrackAcceptedNotification;
use App\Notifications\PendingVibeTrackRejectedNotification;
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

        Event::assertDispatched(PendingVibeTrackCreated::class);
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

        Event::assertDispatched(PendingVibeTrackDeleted::class);
    }

    public function test_that_pending_vibe_track_to_detach_can_be_accepted_by_owner_of_vibe()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $pendingVibeTrack = factory(PendingVibeTrack::class)
            ->states('detach')
            ->create(['vibe_id' => $vibe->id, 'track_id' => $track->id]);

        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->users()->attach($this->user->id, ['owner' => true]);

        $response = $this->delete(route('pending-vibe-track-detach.accept', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('track_vibe', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'auto_related' => false
        ]);
    }

    public function test_that_pending_vibe_track_to_detach_cannot_be_accepted_by_member()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $pendingVibeTrack = factory(PendingVibeTrack::class)
            ->states('detach')
            ->create(['vibe_id' => $vibe->id, 'track_id' => $track->id]);

        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $vibe->users()->attach($this->user->id, ['owner' => false]);

        $response = $this->delete(route('pending-vibe-track-detach.accept', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('track_vibe', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'auto_related' => false
        ]);
    }

    public function test_that_accepting_pending_vibe_track_to_detach_only_detaches_manual_tracks_and_not_auto()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $pendingVibeTrack = factory(PendingVibeTrack::class)
            ->states('detach')
            ->create(['vibe_id' => $vibe->id, 'track_id' => $track->id]);

        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->tracks()->attach($track->id, ['auto_related' => true]);
        $vibe->users()->attach($this->user->id, ['owner' => true]);

        $response = $this->delete(route('pending-vibe-track-detach.accept', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('track_vibe', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'auto_related' => false
        ]);
        $this->assertDatabaseHas('track_vibe', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'auto_related' => true
        ]);
    }

    public function test_that_accepting_a_pending_vibe_track_to_detach_triggers_the_pending_vibe_track_accepted_event()
    {
        Event::fake();

        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $pendingVibeTrack = factory(PendingVibeTrack::class)
            ->states('detach')
            ->create(['vibe_id' => $vibe->id, 'track_id' => $track->id]);

        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->users()->attach($this->user->id, ['owner' => true]);

        $this->delete(route('pending-vibe-track-detach.accept', $pendingVibeTrack));

        Event::assertDispatched(PendingVibeTrackAccepted::class);
    }

    public function test_that_user_of_pending_vibe_track_to_detach_receives_notification_when_pending_track_vibe_is_accepted()
    {
        Notification::fake();

        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $pendingVibeTrack = factory(PendingVibeTrack::class)
            ->states('detach')
            ->create(['vibe_id' => $vibe->id, 'track_id' => $track->id]);

        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->users()->attach($this->user->id, ['owner' => true]);

        $this->delete(route('pending-vibe-track-detach.accept', $pendingVibeTrack));

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

    public function test_that_pending_vibe_track_to_detach_can_be_rejected_by_owner_of_vibe()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $pendingVibeTrack = factory(PendingVibeTrack::class)
            ->states('detach')
            ->create(['vibe_id' => $vibe->id, 'track_id' => $track->id]);

        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->users()->attach($this->user->id, ['owner' => true]);

        $response = $this->delete(route('pending-vibe-track-detach.reject', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $pendingVibeTrack->user_id,
            'attach' => $pendingVibeTrack->attach,
        ]);
        $this->assertDatabaseHas('track_vibe', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'auto_related' => false
        ]);
    }

    public function test_that_pending_vibe_track_to_detach_cannot_be_rejected_by_member_of_vibe()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $pendingVibeTrack = factory(PendingVibeTrack::class)
            ->states('detach')
            ->create(['vibe_id' => $vibe->id, 'track_id' => $track->id]);

        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $vibe->users()->attach($this->user->id, ['owner' => false]);

        $response = $this->delete(route('pending-vibe-track-detach.reject', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $pendingVibeTrack->user_id,
            'attach' => $pendingVibeTrack->attach,
        ]);
        $this->assertDatabaseHas('track_vibe', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'auto_related' => false
        ]);
    }

    public function test_rejecting_a_pending_vibe_track_to_reject_triggers_the_pending_vibe_track_rejected_event()
    {
        Event::fake();

        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $pendingVibeTrack = factory(PendingVibeTrack::class)
            ->states('detach')
            ->create(['vibe_id' => $vibe->id, 'track_id' => $track->id]);

        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->users()->attach($this->user->id, ['owner' => true]);

        $this->delete(route('pending-vibe-track-detach.reject', $pendingVibeTrack));

        Event::assertDispatched(PendingVibeTrackRejected::class);
    }

    public function test_that_user_of_pending_vibe_track_to_detach_receives_notification_when_pending_track_vibe_is_rejected()
    {
        Notification::fake();

        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $pendingVibeTrack = factory(PendingVibeTrack::class)
            ->states('detach')
            ->create(['vibe_id' => $vibe->id, 'track_id' => $track->id]);

        $vibe->tracks()->attach($track->id, ['auto_related' => false]);
        $vibe->users()->attach($this->user->id, ['owner' => true]);

        $this->delete(route('pending-vibe-track-detach.reject', $pendingVibeTrack));

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
