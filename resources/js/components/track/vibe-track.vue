<template>
    <div :class="isPlaying">
        <a @click="playTrack()">
            <img :src="track.album.images[0].url">
        </a>
        <br><br>

        <p v-text="track.name" style="white-space: nowrap; overflow: hidden;"></p>

        <div>
            <div>
                <div v-for="userVibeID in user.allVibesIDsExcept(vibe.id)" v-if="track.vibes.includes(userVibeID)">
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

            <div>
                <form method="POST" :action="vibes.routes.removeTrack(vibe.id, track.vibon_id)" @submit.prevent="onRemoveTrackSubmit(vibe.id, track.vibon_id)">
                    <input v-if="vibe.auto_dj" type="submit" name="track-vibe-destroy" :value="vibes.getVibeName(vibe.id)" style="background:red;" disabled>
                    <input v-else type="submit" name="track-vibe-destroy" :value="vibes.getVibeName(vibe.id)" style="background:red;">
                </form>
                <br>
            </div>
        </div>


        <div v-if="!vibe.auto_dj">
            <div v-if="track.is_voted_by_user">
                <form method="POST" :action="vibes.routes.downvoteTrack(vibe.id, track.vibon_id)" @submit.prevent="onDownvoteTrackSubmit(track.vibon_id)">
                    <input type="submit" name="vote-store" value="Unvote" style="background:red;">
                    {{ track.votes_count }}
                </form>
                <br>
            </div>
            <div v-else>
                <form method="POST" :action="vibes.routes.upvoteTrack(vibe.id, track.vibon_id)" @submit.prevent="onUpvoteTrackSubmit(track.vibon_id)">
                    <input type="submit" name="vote-store" value="Vote">
                    {{ track.votes_count }}
                </form>
                <br>
            </div>
        </div>
    </div>
</template>

<script>
    import user from '../../core/user.js';
    import playback from '../../core/playback.js';
    import vibes from '../../core/vibes.js';
    import Form from '../../classes/Form.js';

    export default {
        props: ['track', 'vibe'],

        data() {
            return {
                playback: playback,
                user: user,
                vibes: vibes,
                removeTrackForm: new Form({}),
                addTrackForm: new Form({}),
                upvoteTrackForm: new Form({}),
                downvoteTrackForm: new Form({})
            }
        },

        methods: {
            playTrack() {
                this.playback.playVibe({
                    playerInstance: this.playback.player,
                    playlist_uri: this.vibe.uri,
                    track_uri: this.track.uri
                });
            },

            onRemoveTrackSubmit(vibeID, trackVibonID) {
                this.vibes.removeTrack(this.removeTrackForm, vibeID, trackVibonID);
            },

            onAddTrackSubmit(vibeID, trackID) {
                this.vibes.addTrack(this.addTrackForm, vibeID, trackID);
            },

            onUpvoteTrackSubmit(trackID) {
                this.vibes.upvoteTrack(this.upvoteTrackForm, this.vibe.id, trackID);
            },

            onDownvoteTrackSubmit(trackID) {
                this.vibes.downvoteTrack(this.downvoteTrackForm, this.vibe.id, trackID);
            }
        },

        computed : {
            isPlaying() {
                if(this.vibes.playingTracks[this.vibe.id] === this.track.id) {
                    return 'playback-play-track playing';
                }

                return 'playback-play-track';
            }
        }
    }
</script>

<style scoped>
    .playing {
        background: green;
    }
</style>