<template>
    <div v-if="this.playback.show" class="playback-buttons">
        <div>
            <img :src="playback.playingTrack.album.images[0].url" alt="">
            <br><br>

            <p v-text="playbackArtistAndTrackName"></p>
        </div>

        <div v-if="userIsVibeOwner">
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
    </div>
</template>

<script>
    import playback from '../core/playback.js';
    import vibes from '../core/vibes.js';

    export default {
        data() {
            return {
                playback: playback,
                vibes: vibes,
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
            },

            userIsVibeOwner() {
                let vibe = this.vibes.all.filter((vibe) => vibe.id === this.playback.vibeID)[0];
                return vibe.destroyable;
            }
        }
    }
</script>