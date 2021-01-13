<?php

namespace Tests\Unit\Controllers;

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

class AttachTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_that_pending_vibe_track_to_attach_can_be_created_by_member_of_a_vibe()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user);

        $response = $this->post(route('pending-vibe-track-attach.store', [
            'vibe' => $vibe,
            'track-api' => $track->api_id,
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('pending_vibe_tracks', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id,
            'attach' => true,
        ]);
    }

    public function test_that_pending_vibe_track_to_attach_cannot_be_created_by_non_member_of_a_vibe()
    {
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();

        $response = $this->post(route('pending-vibe-track-attach.store', [
            'vibe' => $vibe,
            'track-api' => $track->api_id,
        ]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('pending_vibe_tracks', [
            'track_id' => $track->id,
            'vibe_id' => $vibe->id,
            'user_id' => $this->user->id,
            'attach' => true,
        ]);
    }

    public function test_creating_a_pending_vibe_track_to_attach_triggers_the_pending_vibe_track_created_event()
    {
        Event::fake();
        $track = factory(Track::class)->create();
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user);

        $this->post(route('pending-vibe-track-attach.store', [
            'vibe' => $vibe,
            'track-api' => $track->api_id,
        ]));

        Event::assertDispatched(PendingVibeTrackCreated::class);
    }

    public function test_that_pending_vibe_track_to_attach_can_be_deleted_by_user_who_created_it()
    {
        $pendingVibeTrack = factory(PendingVibeTrack::class)
            ->states('attach')
            ->create(['user_id' => $this->user]);

        $response = $this->delete(route('pending-vibe-track-attach.destroy', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $this->user->id,
            'attach' => $pendingVibeTrack->attach,
        ]);
    }

    public function test_that_pending_vibe_track_to_attach_can_be_deleted_by_vibe_owner()
    {
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create();
        $pendingVibeTrack->vibe->users()->attach($this->user->id, ['owner' => true]);

        $response = $this->delete(route('pending-vibe-track-attach.destroy', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $this->user->id,
            'attach' => $pendingVibeTrack->attach,
        ]);
    }

    public function test_that_pending_vibe_track_to_attach_cannot_be_deleted_by_user_who_didnt_create_it()
    {
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create();
        $pendingVibeTrack->vibe->users()->attach(
            factory(User::class)->create()->id,
            ['owner' => true]
        );

        $response = $this->delete(route('pending-vibe-track-attach.destroy', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $pendingVibeTrack->user_id,
            'attach' => $pendingVibeTrack->attach,
        ]);
    }

    public function test_deleting_a_pending_vibe_track_to_attach_triggers_the_pending_vibe_track_deleted_event()
    {
        Event::fake();

        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['user_id' => $this->user]);

        $this->delete(route('pending-vibe-track-attach.destroy', $pendingVibeTrack));

        Event::assertDispatched(PendingVibeTrackDeleted::class);
    }

    public function test_that_pending_vibe_track_to_attach_can_be_accepted_by_owner_of_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['vibe_id' => $vibe]);

        $response = $this->delete(route('pending-vibe-track-attach.accept', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('track_vibe', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $vibe->id,
            'auto_related' => false
        ]);
    }

    public function test_that_pending_vibe_track_to_attach_cannot_be_accepted_by_member_of_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $vibe->users()->attach($this->user->id, ['owner' => false]);
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['vibe_id' => $vibe]);

        $response = $this->delete(route('pending-vibe-track-attach.accept', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('track_vibe', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $vibe->id,
            'auto_related' => false
        ]);
    }

    public function test_that_accepting_a_pending_vibe_track_to_attach_triggers_the_pending_vibe_track_accepted_event()
    {
        Event::fake();

        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['vibe_id' => $vibe]);

        $this->delete(route('pending-vibe-track-attach.accept', $pendingVibeTrack));

        Event::assertDispatched(PendingVibeTrackAccepted::class);
    }

    public function test_that_user_of_pending_vibe_track_to_attach_receives_notification_when_pending_track_vibe_is_accepted()
    {
        Notification::fake();

        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['vibe_id' => $vibe]);

        $this->delete(route('pending-vibe-track-attach.accept', $pendingVibeTrack));

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

    public function test_that_pending_vibe_track_to_attach_can_be_rejected_by_owner_of_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['vibe_id' => $vibe]);

        $response = $this->delete(route('pending-vibe-track-attach.reject', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $pendingVibeTrack->user_id,
            'attach' => $pendingVibeTrack->attach,
        ]);
        $this->assertDatabaseMissing('track_vibe', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'auto_related' => false
        ]);
    }

    public function test_that_pending_vibe_track_to_attach_cannot_be_rejected_by_member_of_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $vibeOwner = factory(User::class)->create();
        $vibe->users()->attach($vibeOwner->id, ['owner' => true]);
        $vibe->users()->attach($this->user->id, ['owner' => false]);
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['vibe_id' => $vibe]);

        $response = $this->delete(route('pending-vibe-track-attach.reject', $pendingVibeTrack));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('pending_vibe_tracks', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'user_id' => $pendingVibeTrack->user_id,
            'attach' => $pendingVibeTrack->attach,
        ]);
        $this->assertDatabaseMissing('track_vibe', [
            'track_id' => $pendingVibeTrack->track_id,
            'vibe_id' => $pendingVibeTrack->vibe_id,
            'auto_related' => false
        ]);
    }

    public function test_rejecting_a_pending_vibe_track_to_attach_triggers_the_pending_vibe_track_rejected_event()
    {
        Event::fake();

        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['vibe_id' => $vibe]);

        $this->delete(route('pending-vibe-track-attach.reject', $pendingVibeTrack));

        Event::assertDispatched(PendingVibeTrackRejected::class);
    }

    public function test_that_pending_vibe_track_to_attach_user_receives_notification_when_pending_track_vibe_is_rejected()
    {
        Notification::fake();

        $vibe = factory(Vibe::class)->create();
        $vibe->users()->attach($this->user->id, ['owner' => true]);
        $pendingVibeTrack = factory(PendingVibeTrack::class)->states('attach')->create(['vibe_id' => $vibe]);

        $this->delete(route('pending-vibe-track-attach.reject', $pendingVibeTrack));

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
