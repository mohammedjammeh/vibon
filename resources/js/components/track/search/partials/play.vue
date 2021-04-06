<template>
    <div>
        <a @click="playSearchTrack()" title="play">
            <img :src="track.album.images[0].url">
        </a>
        <br><br>

        <p class="artist-and-track-name" v-text="artistAndTrackName"></p>
    </div>
</template>

<script>
    import playback from '../../../../core/playback.js';
    import user from '../../../../core/user.js';

    export default {
        props: ['track', 'searchTracks'],

        data() {
            return {
                playback: playback,
            }
        },

        methods: {
            playSearchTrack() {
                this.playback.type = 'search-tracks-list';

                if(user.deviceID !== null) {
                    this.playSearchTrackAction();
                    return;
                }

                this.user.getAttributes().then(() => {
                    this.playSearchTrackAction();
                });
            },

            playSearchTrackAction() {
                this.playback.playTracks({
                    playerInstance: this.playback.player,
                    tracks_uris: this.searchTracks.map(track => track.uri),
                    track_uri: this.track.uri,
                    device_id: user.deviceID,
                });
            }
        },

        computed: {
            artistAndTrackName() {
                return this.track.artists[0].name + ' - ' + this.track.name;
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
