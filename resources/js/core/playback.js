import vibes from './vibes';

const playback = {
    vibes: vibes,

    player: {},
    show: false,
    paused: false,

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
        if (state) {
            this.show = true;

            let trackID = state['track_window']['current_track']['linked_from']['id']
                ? state['track_window']['current_track']['linked_from']['id']
                : state['track_window']['current_track']['id'];

            if(Object.keys(this.vibes.show).length > 0) {
                this.vibes.show.api_tracks = this.vibes.show.api_tracks.map(track => {
                    if (track.id === trackID) {
                        track.active = true;
                        return track;
                    }
                    track.active = false;
                    return track;
                });
            }

            if(state['paused']) {
                this.paused = true
            } else {
                this.paused = false;
            }
        }
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
    }

};

window.playback = playback;
export default playback;