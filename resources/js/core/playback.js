import vibes from './vibes';
import search from './search';
import _ from 'lodash';

const playback = {
    vibes: vibes,
    search: search,

    player: {},
    show: false,
    paused: false,
    playingTrack: {},
    vibePlayingTrackBroadcastData: {},
    type: null,
    vibeID: null,


    routes: {
        'broadcast': 'playback/broadcast'
    },

    playVibe: ({
         playlist_uri,
         track_uri,
         playerInstance: {
             _options: {
                 getOAuthToken,
                 id
             }
         }}) => {
        getOAuthToken(access_token => {
            fetch(`https://api.spotify.com/v1/me/player/play?device_id=${id}`, {
                method: 'PUT',
                body: JSON.stringify({
                    context_uri: playlist_uri,
                    offset: {
                        uri: track_uri
                    },
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${access_token}`
                },
            });
        });
    },

    playTracks: ({
         tracks_uris,
         track_uri,
         playerInstance: {
             _options: {
                 getOAuthToken,
                 id
             }
         }}) => {
        getOAuthToken(access_token => {
            fetch(`https://api.spotify.com/v1/me/player/play?device_id=${id}`, {
                method: 'PUT',
                body: JSON.stringify({
                    uris: tracks_uris,
                    offset: {
                        uri: track_uri
                    },
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${access_token}`
                },
            });
        });
    },

    updateData(state) {
        if (! state) {
            return;
        }

        this.show = true;
        this.paused = state['paused'];
        this.playingTrack = state['track_window']['current_track'];
        let trackID = this.playingTrack['linked_from']['id']
            ? this.playingTrack['linked_from']['id']
            : this.playingTrack['id'];

        if(this.type === 'search-tracks-list') {
            this.vibes.playingID = null;
            this.updateSearchPlayingTrack(trackID);
            return;
        }

        this.updateVibePlayingTrack(trackID);
        this.broadcastVibePlayingTrack(trackID);
    },


    updateSearchPlayingTrack(trackID) {
        this.search.playingTrack = trackID;
    },

    updateVibePlayingTrack(trackID) {
        // if(this.type === 'playlist') {
        //     this.updateVibePlaylistPlayingTrack(trackID);
        // }

        // if(this.type === 'vibe-other') {
            this.updateVibeOtherPlayingTrack(trackID);
        // }
        //
        this.vibes.playingID = this.vibeID;
    },

    updateVibeOtherPlayingTrack(trackID) {
        let vibe = this.vibes.all.filter((vibe) => vibe.id === this.vibeID)[0];

        // let playlist = vibe.api_tracks.playlist;
        // let notOnVibonTracks = vibe.api_tracks.not_on_vibon;
        // let notOnPlaylistTracks = vibe.api_tracks.not_on_playlist;
        // let pendingTracksToAttach = vibe.api_tracks.pending_to_attach;
        // let pendingTracksToDetach = vibe.api_tracks.pending_to_detach;
        //
        // let otherTracks = playlist.concat(notOnVibonTracks).concat(notOnPlaylistTracks).concat(pendingTracksToAttach).concat(pendingTracksToDetach);

        // otherTracks.forEach(track => {
        //     if(track.id === trackID) {
        //         this.vibes.playingTracks[vibe.id] = track.id;
        //     }
        // });


        // make sure 
        for(let key in vibe.api_tracks) {
            if (vibe.api_tracks.hasOwnProperty(key)) {
                vibe.api_tracks[key].forEach(track => {
                    if(track.id === trackID) {
                        this.vibes.playingTracks[vibe.id] = track.id;
                        this.vibes.playingType[vibe.id] = key;
                    }
                });
            }
        }
    },

    broadcastVibePlayingTrack(trackID) {
        if(this.vibePlayingTrackBroadcastDataIsNotDifferent(trackID)) {
            return;
        }

        this.sendBroadcastDataToServer(trackID);
    },

    vibePlayingTrackBroadcastDataIsNotDifferent(trackID) {
        if(this.vibePlayingTrackBroadcastDataHasRightKeysSet()) {
            return this.vibePlayingTrackBroadcastData['track_id'] === trackID &&
                this.vibePlayingTrackBroadcastData['vibe_id'] === this.vibeID &&
                this.vibePlayingTrackBroadcastData['is_track_paused'] === this.paused &&
                this.vibePlayingTrackBroadcastData['type'] === this.type;
        }

        return false;
    },

    vibePlayingTrackBroadcastDataHasRightKeysSet() {
        return this.vibePlayingTrackBroadcastData.hasOwnProperty('vibe_id') &&
            this.vibePlayingTrackBroadcastData.hasOwnProperty('track_id') &&
            this.vibePlayingTrackBroadcastData.hasOwnProperty('is_track_paused') &&
            this.vibePlayingTrackBroadcastData.hasOwnProperty('type');
    },

    sendBroadcastDataToServer: _.debounce(function(trackID) {
        this.vibePlayingTrackBroadcastData['track_id'] = trackID;
        this.vibePlayingTrackBroadcastData['vibe_id'] = this.vibeID;
        this.vibePlayingTrackBroadcastData['is_track_paused'] = this.paused;
        this.vibePlayingTrackBroadcastData['type'] = this.type;

        axios.post(this.routes.broadcast, this.vibePlayingTrackBroadcastData)
            .then(response => {})
            .catch(error => {
                console.log(error.response.data.errors);
            });
    }, 500),

    playOrResume() {
        this.player.resume().then(() => {});
    },

    pause() {
        this.player.pause().then(() => {});
    },

    previous() {
        this.player.previousTrack().then(() => {});
    },

    next() {
        this.player.nextTrack().then(() => {});
    },
};

window.playback = playback;
export default playback;