<template>
    <div :class="isPlaying()">
        <a @click="playTrack()">
            <img :src="track.album.images[0].url">
        </a>
        <br><br>

        <p v-text="track.name" style="white-space: nowrap; overflow: hidden;"></p>

        <div v-for="userVibeID in user.vibesIDs" v-if="track.vibes.includes(userVibeID)">
            <form method="POST" :action="vibes.routes.removeTrack(userVibeID, track.vibon_id)" @submit.prevent="onRemoveTrackSubmit(userVibeID, track.vibon_id)">
                <input type="submit" name="track-vibe-destroy" :value="vibes.getVibeName(userVibeID)" style="background:red;">
            </form>
            <br>
        </div>
        <div v-else>
            <form method="POST" :action="vibes.routes.addTrack(userVibeID, track.id)" @submit.prevent="onAddTrackSubmit(userVibeID, track.id)">
                <input type="submit" name="track-vibe-destroy" :value="vibes.getVibeName(userVibeID)">
            </form>
            <br>
        </div>
    </div>
</template>

<script>
    import user from '../../core/user.js';
    import playback from '../../core/playback.js';
    import vibes from '../../core/vibes.js';
    import search from '../../core/search.js';
    import Form from '../../classes/Form.js';

    export default {
        props: ['track', 'searchTracks'],

        data() {
            return {
                playback: playback,
                search: search,
                user: user,
                vibes: vibes,
                removeTrackForm: new Form({}),
                addTrackForm: new Form({}),
            }
        },

        methods: {
            playTrack() {
                this.playback.playTracks({
                    playerInstance: this.playback.player,
                    tracks_uris: this.searchTracks.map(track => track.uri),
                    track_uri: this.track.uri
                });
            },

            onRemoveTrackSubmit(vibeID, trackVibonID) {
                this.vibes.removeTrack(this.removeTrackForm, vibeID, trackVibonID);
                let track =  this.searchTracks.find(track => track.vibon_id === trackVibonID);
                let trackVibeIndex = track.vibes.indexOf(vibeID);
                if (trackVibeIndex !== -1) track.vibes.splice(trackVibeIndex, 1);
            },

            onAddTrackSubmit(vibeID, trackID) {
                this.vibes.addTrack(this.addTrackForm, vibeID, trackID);
                let track =  this.searchTracks.find(track => track.id === trackID);
                track.vibes.push(vibeID);
            },

            isPlaying() {
                if(this.search.playingTrack === this.track.id) {
                    return 'playback-play-track-search playing';
                }
                return 'playback-play-track-search';
            }
        }
    }
</script>

<style scoped>
    .playing {
        background: green;
    }
</style>