<template>
    <div v-if="this.playback.show" class="playback-buttons">
        <div>
            <img :src="playback.playingTrack.album.images[0].url" alt="">
            <br><br>

            <p v-text="playbackArtistAndTrackName"></p>
        </div>

        <div v-if="playback.paused" class="playback-resume">
            <a @click="playOrResume">Play</a>
            <br><br>
        </div>

        <div v-else class="playback-pause">
            <a @click="pause">Pause</a>
            <br><br>
        </div>

        <div class="playback-previous">
            <a @click="previous">Previous</a>
            <br><br>
        </div>

        <div class="playback-next">
            <a @click="next">Next</a>
            <br><br>
        </div>
    </div>
</template>

<script>
    import playback from '../core/playback.js';

    export default {
        data() {
            return {
                playback: playback,
            }
        },

        methods: {
            playOrResume() {
                this.playback.playOrResume();
            },

            pause() {
                this.playback.pause();
            },

            previous() {
                this.playback.previous();
            },

            next() {
                this.playback.next();
            }
        },

        computed: {
            playbackArtistAndTrackName() {
                return this.playback.playingTrack.artists[0].name + ' - ' + playback.playingTrack.name;
            }
        }
    }
</script>