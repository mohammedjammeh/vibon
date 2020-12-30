<template>
    <div>
        <a @click="playSearchTrack()" title="play">
            <img :src="track.album.images[0].url">
        </a>
        <br><br>

        <p v-text="track.name" style="white-space: nowrap; overflow: hidden;"></p>
    </div>
</template>

<script>
    import playback from '../../../../core/playback.js';

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

                this.playback.playTracks({
                    playerInstance: this.playback.player,
                    tracks_uris: this.searchTracks.map(track => track.uri),
                    track_uri: this.track.uri
                });
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
