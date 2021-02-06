import vibes from './core/vibes.js';
import user from './core/user.js';
import playback from './core/playback.js';

const actions = {
    updateVibe(data) {
        vibes.get(data.vibe)
            .then((response) => {
                vibes.updateVibeDataForUpdatedVibe(response.vibe);
                vibes.updateShowData();
                vibes.sortVibesOrder();
            });

        if (this.showingVibeIs(data.vibe) && this.isReadyToBeDisplayed(data.message)) {
            this.updateMessage(data.message);
        }
    },

    updateTrack(data, action) {
        vibes.get(data.vibe)
            .then((response) => {
                response.track = {'id': data.track};
                vibes.updateTrackData(response, action);
                vibes.updateShowData();
            });
    },

    showingVibeIs(vibe) {
        return vibes.showID === vibe;
    },

    isReadyToBeDisplayed(message) {
        return message !== undefined;
    },

    updateMessage(message) {
        vibes.message = message;
        setTimeout(() => vibes.message = '', 20000);
    }
};


// Vibe
Echo.channel('vibe.created')
    .listen('VibeCreated', (data) => {
        vibes.get(data.vibe)
            .then((response) => {
                vibes.all.push(response.vibe);
            });
    });

Echo.channel('vibe.updated')
    .listen('VibeUpdated', (data) => {
        actions.updateVibe(data);
    });

Echo.channel('vibe.deleted')
    .listen('VibeDeleted', (data) => {
        vibes.all = vibes.all.filter(vibe => vibe.id !== data.vibe);
        if (vibes.showID === data.vibe) {
            vibes.show = {};
            vibes.deletedMessage = data.message;
        }
    });


// Auto Vibe
Echo.channel('auto.vibe.refreshed')
    .listen('AutoVibeRefreshed', (data) => {
        actions.updateVibe(data);
    });


// Track Vibe
Echo.channel('track.vibe.stored')
    .listen('TrackVibeStored', (data) => {
        actions.updateTrack(data, vibes.addVibeToTrackVibes);
    });

Echo.channel('track.vibe.destroyed')
    .listen('TrackVibeDestroyed', (data) => {
        actions.updateTrack(data, vibes.removeVibeFromTrackVibes);
    });

Echo.channel('pending_attach_vibe_track.created')
    .listen('PendingAttachVibeTrackCreated', (data) => {
        actions.updateTrack(data, vibes.addVibeToTrackPendingVibesToAttach);
    });

Echo.channel('pending_attach_vibe_track.deleted')
    .listen('PendingAttachVibeTrackDeleted', (data) => {
        actions.updateTrack(data, vibes.removeVibeFromTrackPendingVibesToAttach);
    });

Echo.channel('pending_detach_vibe_track.created')
    .listen('PendingDetachVibeTrackCreated', (data) => {
        actions.updateTrack(data, vibes.addVibeToTrackPendingVibesToDetach);
    });

Echo.channel('pending_detach_vibe_track.deleted')
    .listen('PendingDetachVibeTrackDeleted', (data) => {
        actions.updateTrack(data, vibes.removeVibeFromTrackPendingVibesToDetach);
    });


//Vote
Echo.channel('track.voted.up')
    .listen('TrackVotedUp', (data) => {
        actions.updateVibe(data);
    });

//Vibe Synchronisation
Echo.channel('track.voted.down')
    .listen('TrackVotedDown', (data) => {
        actions.updateVibe(data);
    });

Echo.channel('playlist.synchronised.with.vibe.tracks')
    .listen('PlaylistSynchronisedWithVibeTracks', (data) => {
        actions.updateVibe(data);
    });


// Join Requests
Echo.channel('join.request.sent')
    .listen('JoinRequestSent', (data) => {
        actions.updateVibe(data);
    });

Echo.channel('join.request.cancelled')
    .listen('JoinRequestCancelled', (data) => {
        actions.updateVibe(data);
    });

Echo.channel( 'join.request.accepted')
    .listen('JoinRequestAccepted', (data) => {
        actions.updateVibe(data);
    });

Echo.channel('join.request.rejected')
    .listen('JoinRequestRejected', (data) => {
        actions.updateVibe(data);
    });

// User Vibe
Echo.channel('user.joined.vibe')
    .listen('UserJoinedVibe', (data) => {
        actions.updateVibe(data);
    });

Echo.channel('user.left.vibe')
    .listen('UserLeftVibe', (data) => {
        actions.updateVibe(data);
    });

Echo.channel('user.removed.from.vibe')
    .listen('UserRemovedFromVibe', (data) => {
        actions.updateVibe(data);
    });


// Playback
Echo.channel('playback.updated')
    .listen('PlaybackUpdated', (data) => {
        playback.paused = data.isTrackPaused;
        playback.type = data.type;
        playback.vibeID = data.vibeId;

        playback.updateVibePlayingTrack(data.trackId)
    });





