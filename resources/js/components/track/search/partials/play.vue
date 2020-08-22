<template>
    <div>
        <a @click="playTrack()">
            <img :src="track.album.images[0].url">
        </a>)
        <br><br>

        <p v-text="track.name" style="white-space: nowrap; overflow: hidden;"></p>
    </div>
</template>

<script>
    import playback from '../../../../core/playback.js';

    export default {
        props: ['vibe', 'track', 'searchTracks'],

        data() {
            return {
                playback: playback,
            }
        },

        methods: {
            playTrack() {
                this.playback.playTracks({
                    playerInstance: this.playback.player,
                    tracks_uris: this.searchTracks.map(track => track.uri),
                    track_uri: this.track.uri
                });
            }
        }
    }
</script>
