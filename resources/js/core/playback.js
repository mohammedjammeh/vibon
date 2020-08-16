import vibes from './vibes';
import search from './search';

const playback = {
    vibes: vibes,
    search: search,

    player: {},
    show: false,
    paused: false,
    playingTrack: {},

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
            this.paused = state['paused'];

            this.playingTrack = state['track_window']['current_track'];
            
            let trackID = this.playingTrack['linked_from']['id']
                ? this.playingTrack['linked_from']['id']
                : this.playingTrack['id'];

            let vibeURI = state['context']['uri'];

            if(vibeURI === null) {
                this.updateSearchPlayingTracks(trackID);
            } else {
                this.updateVibePlayingTracks(trackID, vibeURI);
            }
        }
    },

    updateSearchPlayingTracks(trackID) {
        this.search.playingTrack = trackID;
    },

    updateVibePlayingTracks(trackID, vibeURI) {
        if(Object.keys(this.vibes.all).length > 0) {
            this.vibes.all.map((vibe) => {
                if(this.format(vibe.uri) === this.format(vibeURI)) {
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

    format(vibeURI) {
        let splitted = vibeURI.split(':');
        return splitted[splitted.length - 1];
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