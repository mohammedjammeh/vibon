<template>
    <div>
        <div v-if="this.vibes.show.destroyable">
            <a v-if="isPlaylistTrack" @click="playPlaylistTracks()" title="play">
                <img :src="track.album.images[0].url">
            </a>
            <a v-else @click="playOtherVibeTracks()" title="play">
                <img :src="track.album.images[0].url">
            </a>
        </div>
        <div v-else>
            <img :src="track.album.images[0].url">
        </div>

        <br>

        <p class="artist-and-track-name" v-text="artistAndTrackName"></p>

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
                user: user,
                vibes: vibes,
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

                this.user.getAttributes().then(() => {
                    this.playPlaylistTrackAction();
                });
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

                user.getAttributes().then(() => {
                    this.playOtherVibeTracksAction(trackUris);
                });
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

    .artist-and-track-name {
        white-space: nowrap;
        overflow: hidden;
    }
</style>
