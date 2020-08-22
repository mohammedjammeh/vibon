<template>
    <div :class="isPlaying">
        <play :track="track" :vibe="vibe"></play>

        <div>
            <h4>Manual Vibes</h4>
            <div v-for="userVibeID in user.manualVibesIDs">
                <remove-button :userVibeID="userVibeID" :trackID="track.vibon_id" v-if="track.vibes.includes(userVibeID)"></remove-button>
                <add-button :userVibeID="userVibeID" :trackID="track.id" v-else></add-button>
            </div>

            <h4>Auto Vibes</h4>
            <div v-for="userVibeID in user.autoVibesIDs">
                <remove-button :userVibeID="userVibeID" :trackID="track.vibon_id" v-if="track.vibes.includes(userVibeID)"></remove-button>
                <add-button :userVibeID="userVibeID" :trackID="track.id" v-else></add-button>
            </div>
        </div>

        <div v-if="!vibe.auto_dj">
            <downvote-button  :vibe="vibe" :track="track" v-if="track.is_voted_by_user"></downvote-button>
            <upvote-button :vibe="vibe" :track="track" v-else></upvote-button>
        </div>
    </div>
</template>

<script>
    import user from '../../core/user.js';
    import vibes from '../../core/vibes.js';
    import play from './vibe/partials/play';
    import addButton from './vibe/buttons/add';
    import removeButton from './vibe/buttons/remove';
    import upvoteButton from './vibe/buttons/upvote';
    import downvoteButton from './vibe/buttons/downvote';


    export default {
        props: ['track', 'vibe'],

        components: {
            'play' : play,
            'add-button': addButton,
            'remove-button': removeButton,
            'upvote-button': upvoteButton,
            'downvote-button': downvoteButton
        },

        data() {
            return {
                user: user,
                vibes: vibes
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