<template>
    <div>
        <h4>Search Results..</h4>
        <div class="api-tracks" v-if="tracksAndVibesAreloaded()">
            <div v-for="track in this.tracks" :class="isActive(track)">
                <a @click="playTrack(track.uri)">
                    <img v-bind:src="track.album.images[0].url">
                </a>

                <p v-text="track.name" style="white-space: nowrap; overflow: hidden;"></p>

                <div v-for="userVibeID in user.vibesIDs">
                    <div v-if="track.vibes.includes(userVibeID)">
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
            </div>
        </div>
        <div v-else>
            <br><br>
            <p>loading..</p>
        </div>
    </div>
</template>

<script>
    import search from '../../core/search';
    import vibes from '../../core/vibes';
    import user from '../../core/user';
    import playback from '../../core/playback.js';
    import Form from '../../classes/Form';

    export default {
        data() {
            return {
                vibes: vibes,
                user: user,
                playback: playback,
                search: search,
                input: this.$route.params.input,
                tracks: [],
                removeTrackForm: new Form({}),
                addTrackForm: new Form({}),
            }
        },

        created() {
            this.search.setRoute(this.input);

            return new Promise((resolve, reject) => {
                axios.get(this.search.route)
                    .then(response => {
                        this.tracks = response.data;
                        resolve(response.data);
                    })
                    .catch(error => {
                        reject(error.response.data.errors);
                    });
            });
        },

        methods: {
            tracksAndVibesAreloaded() {
                return Object.keys(this.tracks).length > 0 && Object.keys(this.vibes.all).length > 0;
            },

            onRemoveTrackSubmit(vibeID, trackVibonID) {
                this.vibes.removeTrack(this.removeTrackForm, vibeID, trackVibonID);
                let track =  this.tracks.find(track => track.vibon_id === trackVibonID);
                let trackVibeIndex = track.vibes.indexOf(vibeID);
                if (trackVibeIndex !== -1) track.vibes.splice(trackVibeIndex, 1);
            },

            onAddTrackSubmit(vibeID, trackID) {
                this.vibes.addTrack(this.addTrackForm, vibeID, trackID);
                let track =  this.tracks.find(track => track.id === trackID);
                track.vibes.push(vibeID);
            },

            playTrack(trackURI) {
                this.playback.playTracks({
                    playerInstance: this.playback.player,
                    tracks_uris: this.tracks.map(track => track.uri),
                    track_uri: trackURI
                });
            },

            isActive(track) {
                if(track.active) {
                    return 'playback-play-track-search active';
                }
                return 'playback-play-track-search';
            }
        }
    }
</script>

<style scoped>
    .active {
        background: green;
    }
</style>