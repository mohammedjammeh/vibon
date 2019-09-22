<?php

namespace Tests\Browser\Vibe;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Vibe;
use App\User;
use App\Genre;
use App\Track;
use App\JoinRequest;
use App\Events\JoinRequestSent;
use App\MusicAPI\Tracks;

class ShowPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_the_vibe_show_page_displays_the_correct_attributes_of_a_vibe()
    {
        $user = factory(User::class)->create();
        $vibe = factory(Vibe::class)->create([
            'open' => true,
            'auto_dj' => false
        ]);
        $user->vibes()->attach($vibe->id, ['owner' => true]);

        $this->browse(function (Browser $browser) use($user, $vibe) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $browser->loginAs($user)
                ->visit($vibe->path)
                ->assertSee($vibe->name)
                ->assertSee($vibe->description)
                ->assertSee('Opened')
                ->assertSee('Manual DJ');
        });
    }

    public function test_only_a_member_of_a_vibe_can_see_the_edit_link_and_has_the_authority_to_edit_the_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $member = factory(User::class)->create();
        $nonMember = factory(User::class)->create();
        $vibe->users()->attach($member->id, ['owner' => false]);

        $this->browse(function (Browser $browser) use($vibe, $member, $nonMember) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $browser->loginAs($member)
                ->visit($vibe->path)
                ->assertSeeLink('Edit')
                ->clickLink('Edit')
                ->assertUrlIs(route('vibe.edit', ['vibe' => $vibe->id]));

            $browser->loginAs($nonMember)
                ->visit($vibe->path)
                ->assertDontSeeLink('Edit');
        });
    }

    public function test_only_owner_of_a_vibe_can_see_the_delete_and_refresh_buttons_as_well_as_join_requests()
    {
        $vibe = factory(Vibe::class)->create(['auto_dj' => true]);
        $member = factory(User::class)->create();
        $owner = factory(User::class)->create();
        $joinRequest = factory(JoinRequest::class)->create(['vibe_id' => $vibe->id]);
        $vibe->users()->attach($member->id, ['owner' => false]);
        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->browse(function (Browser $browser) use($vibe, $member, $owner, $joinRequest) {
            $this->loadVibesAsPlaylists([$vibe])->first();

            $deleteVibeBtn = 'input[name="vibe-delete"]';
            $refreshVibeBtn = 'input[name="vibe-tracks-update"]';
            $acceptJoinRequestBtn = 'input[name="accept"]';
            $rectJoinRequestBtn = 'input[name="reject"]';

            $browser->loginAs($owner)
                ->visit($vibe->path)
                ->assertVisible($refreshVibeBtn)
                ->assertVisible($deleteVibeBtn)
                ->assertVisible($acceptJoinRequestBtn)
                ->assertVisible($rectJoinRequestBtn)
                ->assertSee($joinRequest->user->username);

            $browser->loginAs($member)
                ->visit($vibe->path)
                ->assertMissing($refreshVibeBtn)
                ->assertMissing($deleteVibeBtn)
                ->assertMissing($acceptJoinRequestBtn)
                ->assertMissing($rectJoinRequestBtn)
                ->assertDontSee($joinRequest->user->username);
        });
    }

    public function test_vibe_owner_can_delete_a_vibe()
    {
        $vibe = factory(Vibe::class)->create([]);
        $owner = factory(User::class)->create();
        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->browse(function (Browser $browser) use($vibe, $owner) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $deleteVibeBtn = 'input[name="vibe-delete"]';
            $browser->loginAs($owner)
                ->visit($vibe->path)
                ->assertVisible($deleteVibeBtn)
                ->press($deleteVibeBtn)
                ->assertUrlIs(route('index') . '/')
                ->assertSee($vibe->name . ' has been deleted.');
        });
    }

    public function test_vibe_owner_can_accept_a_join_request()
    {
        $vibe = factory(Vibe::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($owner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create(['vibe_id' => $vibe->id]);
        event(new JoinRequestSent($joinRequest));

        $this->browse(function (Browser $browser) use($vibe, $owner, $joinRequest) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $browser->loginAs($owner)
                ->visit($vibe->path)
                ->press('Accept')
                ->assertSee($joinRequest->user->username)
                ->assertVisible('input[name="vibe-member-remove"]');
        });
    }

    public function test_vibe_owner_can_reject_a_join_request()
    {
        $vibe = factory(Vibe::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($owner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create(['vibe_id' => $vibe->id]);
        event(new JoinRequestSent($joinRequest));

        $this->browse(function (Browser $browser) use($vibe, $owner, $joinRequest) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $browser->loginAs($owner)
                ->visit($vibe->path)
                ->press('Reject')
                ->assertDontSee($joinRequest->user->username)
                ->assertMissing('input[name="vibe-member-remove"]');
        });
    }

    public function test_member_of_a_vibe_can_leave_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $member = factory(User::class)->create();
        $vibe->users()->attach($member->id, ['owner' => false]);

        $this->browse(function (Browser $browser) use($vibe, $member) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $browser->loginAs($member)
                ->visit($vibe->path)
                ->assertVisible('input[name="vibe-leave"]')
                ->assertMissing('input[name="vibe-join-destroy"]')
                ->assertMissing('input[name="vibe-store"]')
                ->press('input[name="vibe-leave"]');

            $browser->loginAs($member)
                ->visit($vibe->path)
                ->assertMissing('input[name="vibe-leave"]')
                ->assertMissing('input[name="vibe-join-destroy"]')
                ->assertVisible('input[name="vibe-store"]');
        });
    }

    public function test_user_who_sends_a_join_request_to_vibe_can_cancel_the_join_request()
    {
        $vibe = factory(Vibe::class)->create();
        $user = factory(User::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($owner->id, ['owner' => true]);
        $joinRequest = factory(JoinRequest::class)->create([
            'vibe_id' => $vibe->id,
            'user_id' => $user->id
        ]);
        event(new JoinRequestSent($joinRequest));

        $this->browse(function (Browser $browser) use($vibe, $user) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $browser->loginAs($user)
                ->visit($vibe->path)
                ->assertVisible('input[name="vibe-join-destroy"]')
                ->assertMissing('input[name="vibe-leave"]')
                ->assertMissing('input[name="vibe-store"]')
                ->press('input[name="vibe-join-destroy"]');

            $browser->loginAs($user)
                ->visit($vibe->path)
                ->assertMissing('input[name="vibe-leave"]')
                ->assertMissing('input[name="vibe-join-destroy"]')
                ->assertVisible('input[name="vibe-store"]');
        });
    }

    public function test_user_who_is_not_a_member_and_has_not_sent_a_join_request_to_a_vibe_can_send_a_request_to_join_the_vibe()
    {
        $vibe = factory(Vibe::class)->create(['open' => false]);
        $user = factory(User::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->browse(function (Browser $browser) use($vibe, $user) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $browser->loginAs($user)
                ->visit($vibe->path)
                ->assertVisible('input[name="vibe-store"]')
                ->assertMissing('input[name="vibe-join-destroy"]')
                ->assertMissing('input[name="vibe-leave"]')
                ->press('input[name="vibe-store"]');

            $browser->loginAs($user)
                ->visit($vibe->path)
                ->assertVisible('input[name="vibe-join-destroy"]')
                ->assertMissing('input[name="vibe-leave"]')
                ->assertMissing('input[name="vibe-store"]');
        });
    }

    public function test_user_can_just_join_a_vibe_that_is_open()
    {
        $vibe = factory(Vibe::class)->create(['open' => true]);
        $user = factory(User::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->browse(function (Browser $browser) use($vibe, $user) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $joinVibeInput = 'input[name="vibe-store"]';
            $browser->loginAs($user)
                ->visit($vibe->path)
                ->assertVisible($joinVibeInput)
                ->assertInputValue($joinVibeInput, 'Join Vibe')
                ->press('input[name="vibe-store"]');

            $browser->loginAs($user)
                ->visit($vibe->path)
                ->assertSee($user->username)
                ->assertVisible('input[name="vibe-leave"]');
        });
    }

    public function test_user_has_to_send_a_join_request_for_a_vibe_that_is_not_open()
    {
        $vibe = factory(Vibe::class)->create(['open' => false]);
        $user = factory(User::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->browse(function (Browser $browser) use($vibe, $user) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $joinVibeInput = 'input[name="vibe-store"]';
            $browser->loginAs($user)
                ->visit($vibe->path)
                ->assertVisible($joinVibeInput)
                ->assertInputValue($joinVibeInput, 'Send Join Request')
                ->press('input[name="vibe-store"]');

            $browser->loginAs($user)
                ->visit($vibe->path)
                ->assertVisible('input[name="vibe-join-destroy"]');
        });
    }

    public function test_vibe_members_are_displayed_on_a_vibe_page()
    {
        $vibe = factory(Vibe::class)->create();
        $users = factory(User::class, 2)->create();
        $vibe->users()->attach($users->pluck('id'), ['owner' => false]);

        $this->browse(function (Browser $browser) use($vibe, $users) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $browser->loginAs($users->first())
                ->visit($vibe->path)
                ->assertSee($users->first()->username)
                ->assertSee($users->last()->username);
        });
    }

    public function test_vibe_owner_can_remove_a_member_from_a_vibe()
    {
        $vibe = factory(Vibe::class)->create();
        $member = factory(User::class)->create();
        $owner = factory(User::class)->create();
        $vibe->users()->attach($member->id, ['owner' => false]);
        $vibe->users()->attach($owner->id, ['owner' => true]);

        $this->browse(function (Browser $browser) use($vibe, $member, $owner) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $removeMemberBtn = 'input[name="vibe-member-remove"]';
            $browser->loginAs($owner)
                ->visit($vibe->path)
                ->assertSee($member->username)
                ->assertVisible($removeMemberBtn)
                ->press($removeMemberBtn)
                ->assertDontSee($member->username);
        });
    }

    public function test_vibe_member_cannot_remove_other_members()
    {
        $vibe = factory(Vibe::class)->create();
        $members = factory(User::class, 2)->create();
        $vibe->users()->attach($members->pluck('id'), ['owner' => false]);

        $this->browse(function (Browser $browser) use($vibe, $members) {
            $this->loadVibesAsPlaylists([$vibe])->first();
            $removeMemberBtn = 'input[name="vibe-member-remove"]';

            $browser->loginAs($members->first())
                ->visit($vibe->path)
                ->assertSee($members->last()->username)
                ->assertMissing($removeMemberBtn);
        });
    }

//    public function test_show_page_of_an_auto_vibe_displays_auto_tracks_and_not_regular_tracks()
//    {
//        $vibe = factory(Vibe::class)->create(['auto_dj' => true]);
//        $user = factory(User::class)->create();
//        $autoTracks = factory(Track::class, 2)->create();
//        $autoTracks->map(function ($track) {
//            $genres = factory(Genre::class, 2)->create();
//            return $track->genres()->attach($genres->pluck('id'), []);
//        });
//        $vibe->tracks()->attach($autoTracks->pluck('id'), ['auto_related' => true]);
//        $vibe->users()->attach($user->id, ['owner' => false]);
//
//        $this->browse(function (Browser $browser) use($vibe, $autoTracks, $user) {
//            $autoTracks = $this->tracksAPI($autoTracks);
//            $this->loadVibesAsPlaylists([$vibe])->first();
//            $addTrackToVibeBtn = 'input[name="track-vibe-store"]';
//            $removeTrackFromVibeBtn = 'input[name="track-vibe-destroy"]';
//            $browser->loginAs($user)
//                ->visit($vibe->path)
//                ->assertSee($autoTracks->first()->name)
//                ->assertSee($autoTracks->last()->name)
//                ->assertMissing($addTrackToVibeBtn)
//                ->assertMissing($removeTrackFromVibeBtn);
//        });
//    }

//    public function test_show_page_of_vibe_that_is_not_auto_displays_regular_tracks_and_not_auto_tracks()
//    {
//        $vibe = factory(Vibe::class)->create(['auto_dj' => false]);
//        $user = factory(User::class)->create();
//        $tracks = factory(Track::class, 2)->create();
//        $vibe->tracks()->attach($tracks->pluck('id'), ['auto_related' => false]);
//        $vibe->users()->attach($user->id, ['owner' => false]);
//
//        $this->browse(function (Browser $browser) use($vibe, $tracks, $user) {
//            $tracks = $this->tracksAPI($tracks);
//            $this->loadVibesAsPlaylists([$vibe])->first();
//            $removeTrackFromVibeBtn = 'input[name="track-vibe-destroy"]';
//            $browser->loginAs($user)
//                ->visit($vibe->path)
//                ->assertSee($tracks->first()->name)
//                ->assertSee($tracks->last()->name)
//                ->assertVisible($removeTrackFromVibeBtn);
//        });
//    }

    protected function tracksAPI($tracks)
    {
        return app(Tracks::class)->load($tracks);
    }
}
