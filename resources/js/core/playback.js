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
        let vibeURI = state['context']['uri'];
        let trackID = this.playingTrack['linked_from']['id']
            ? this.playingTrack['linked_from']['id']
            : this.playingTrack['id'];

        if(this.isNull(vibeURI)) {
            this.updateSearchPlayingTrack(trackID);
            return;
        }

        let vibeID = this.getVibeID(vibeURI);
        this.updateVibePlayingTrack(vibeID, trackID);
        this.broadcastVibePlayingTrack(vibeID, trackID);
    },

    isNull(vibeURI) {
        return vibeURI === null;
    },

    updateSearchPlayingTrack(trackID) {
        this.search.playingTrack = trackID;
    },

    updateVibePlayingTrack(vibeID, trackID) {
        if(Object.keys(this.vibes.all).length > 0) {
            this.vibes.all.map((vibe) => {
                if(this.getVibeID(vibe.uri) === vibeID) {
                    vibe.api_tracks.forEach(track => {
                        if(track.id === trackID) {
                            this.vibes.playingTracks[vibe.id] = track.id;
                        }
                    });

                    this.vibes.playingID = vibe.id;
                }
            });
        }
    },

    getVibeID(vibeURI) {
        let splittedUri = vibeURI.split(':');
        let lastIndex = splittedUri.length - 1;
        return splittedUri[lastIndex];
    },

    broadcastVibePlayingTrack(vibeID, trackID) {
        if(this.vibePlayingTrackBroadcastDataIsNotDifferent(vibeID, trackID)) {
            return;
        }

        this.sendBroadcastDataToServer(vibeID, trackID);
    },

    sendBroadcastDataToServer: _.debounce(function(vibeID, trackID) {
        this.vibePlayingTrackBroadcastData['vibe_id'] = vibeID;
        this.vibePlayingTrackBroadcastData['track_id'] = trackID;
        this.vibePlayingTrackBroadcastData['is_track_paused'] = this.paused;
        axios.post(this.routes.broadcast, this.vibePlayingTrackBroadcastData)
            .then(response => {})
            .catch(error => {
                console.log(error.response.data.errors);
            });
    }, 500),

    vibePlayingTrackBroadcastDataIsNotDifferent(vibeID, trackID) {
        if(this.vibePlayingTrackBroadcastDataHasRightKeysSet()) {
            return this.vibePlayingTrackBroadcastData['vibe_id'] === vibeID &&
                this.vibePlayingTrackBroadcastData['track_id'] === trackID &&
                this.vibePlayingTrackBroadcastData['is_track_paused'] === this.paused;
        }

        return false;
    },

    vibePlayingTrackBroadcastDataHasRightKeysSet() {
        return this.vibePlayingTrackBroadcastData.hasOwnProperty('vibe_id') &&
            this.vibePlayingTrackBroadcastData.hasOwnProperty('track_id') &&
            this.vibePlayingTrackBroadcastData.hasOwnProperty('is_track_paused');
    },

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