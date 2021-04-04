<template>
    <div>
        <a v-if="isPlaylistTrack" @click="playPlaylistTracks()" title="play">
            <img :src="track.album.images[0].url">
        </a>
        <a v-else @click="playOtherVibeTracks()" title="play">
            <img :src="track.album.images[0].url">
        </a>

        <br><br>

        <p v-text="artistAndTrackName" style="white-space: nowrap; overflow: hidden;"></p>

    </div>
</template>

<script>
    import playback from '../../../../core/playback.js';
    import user from '../../../../core/user.js';
    import vibes from '../../../../core/vibes.js';

    export default {
        props: ['track', 'type'],

        data() {
            return {
                playback: playback,
            }
        },

        methods: {
            playPlaylistTracks() {
                this.playback.type = this.type;
                this.playback.vibeID = vibes.show.id;

                if(user.deviceID !== null) {
                    this.playPlaylistTrackAction();
                    return;
                }

                axios.get(user.routes.attributes)
                    .then(response => {
                        user.deviceID = response.data.device_id;
                        this.playPlaylistTrackAction();
                    })
                    .catch(errors => console.log(errors));
            },

            playOtherVibeTracks() {
                this.playback.type = this.type;
                this.playback.vibeID = vibes.show.id;

                let tracks = vibes.show.api_tracks[this.type];
                let trackUris = tracks.map((track) => track.uri);

                if(user.deviceID !== null) {
                    this.playOtherVibeTracksAction(trackUris);
                    return;
                }

                axios.get(user.routes.attributes)
                    .then(response => {
                        user.deviceID = response.data.device_id;
                        this.playOtherVibeTracksAction(trackUris);
                    })
                    .catch(errors => console.log(errors));
            },

            playPlaylistTrackAction() {
                this.playback.playPlaylist({
                    playerInstance: this.playback.player,
                    playlist_uri: vibes.show.uri,
                    track_uri: this.track.uri,
                    device_id: user.deviceID,
                });
            },

            playOtherVibeTracksAction(trackUris) {
                this.playback.playTracks({
                    playerInstance: this.playback.player,
                    tracks_uris: trackUris,
                    track_uri: this.track.uri,
                    device_id: user.deviceID,
                });
            }
        },

        computed: {
            artistAndTrackName() {
                return this.track.artists[0].name + ' - ' + this.track.name;
            },

            isPlaylistTrack() {
                return this.type === 'playlist';
            }
        }
    }
</script>

<style scoped>
    img {
        max-width: 100%;
    }

    div a {
        transition: all 0.5s;
    }

    div a:hover {
        cursor: pointer;
        opacity: 0.8;
    }
</style>
