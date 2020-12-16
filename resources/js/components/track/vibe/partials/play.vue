<template>
    <div>
        <a @click="playTrack()">
            <img :src="track.album.images[0].url">
        </a>
        <br><br>

        <h5 v-text="track.name" style="white-space: nowrap; overflow: hidden;"></h5>
    </div>
</template>

<script>
    import playback from '../../../../core/playback.js';

    export default {
        props: ['vibe', 'track'],

        data() {
            return {
                playback: playback,
            }
        },

        methods: {
            playTrack() {
                this.playback.playVibe({
                    playerInstance: this.playback.player,
                    playlist_uri: this.vibe.uri,
                    track_uri: this.track.uri
                });
            }
        }
    }
</script>

<style>
    img {
        max-width: 100%;
    }
</style>
