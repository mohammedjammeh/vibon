<template>
    <div :class="isPlaying">
        <play :track="track" :playlistTrack="this.playlistTrack"></play>

        <div>
            <!--<p>Manual</p>-->
            <div v-for="userVibeID in user.manualVibesIDs">
                <!--the remove will also need to be pended if user is not owner-->
                <remove-button
                    v-if="track.vibes.includes(userVibeID)"
                    :userVibeID="userVibeID"
                    :trackID="track.vibon_id"
                >
                </remove-button>


                <cancel-pend-track-button
                    v-else-if="track.pending_vibes.includes(userVibeID)"
                    :vibeID="userVibeID"
                    :trackID="track.vibon_id"
                >
                </cancel-pend-track-button>



                <add-button
                    v-else
                    :vibeID="userVibeID"
                    :trackID="track.id"
                >
                </add-button>
            </div>

            <!--<p>Auto</p>-->
            <div v-for="userVibeID in user.autoVibesIDs">
                <remove-button :userVibeID="userVibeID" :trackID="track.vibon_id" v-if="track.vibes.includes(userVibeID)"></remove-button>
                <p v-else-if="track.pending_vibes.includes(userVibeID)">pending..</p>
                <add-button :userVibeID="userVibeID" :trackID="track.id" v-else></add-button>
            </div>
        </div>

        <!--<br>-->

        <div v-if="!vibes.show.auto_dj">
            <downvote-button  :vibe="vibes.show" :track="track" v-if="track.is_voted_by_user"></downvote-button>
            <upvote-button :vibe="vibes.show" :track="track" v-else></upvote-button>
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
    import cancelPendTrackButton from './vibe/buttons/cancel-pend-track';


    export default {
        props: ['track', 'playlistTrack'],

        components: {
            'play' : play,
            'add-button': addButton,
            'remove-button': removeButton,
            'upvote-button': upvoteButton,
            'downvote-button': downvoteButton,
            'cancel-pend-track-button': cancelPendTrackButton,
        },

        data() {
            return {
                user: user,
                vibes: vibes
            }
        },

        computed : {
            isPlaying() {
                if(this.vibes.playingTracks[vibes.show.id] === this.track.id) {
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