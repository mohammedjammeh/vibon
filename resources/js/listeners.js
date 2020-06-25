import vibes from './core/vibes.js';

const actions = {
    updateVibe(data) {
        vibes.get(data.vibe)
            .then((response) => {
                vibes.updateVibeDataForUpdatedVibe(response.vibe);
                vibes.updateShowData();
            });

        if (vibes.showID === data.vibe) {
            vibes.message = data.message;
            setTimeout(() => vibes.message = '', 20000);
        }
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
        actions.updateVibe(data);
    });

Echo.channel('track.vibe.destroyed')
    .listen('TrackVibeDestroyed', (data) => {
        actions.updateVibe(data);
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