<template>
    <div>
        <form method="POST" :action="vibes.routes.downvoteTrack(vibe.id, track.vibon_id)" @submit.prevent="onDownvoteTrackSubmit(track.vibon_id)">
            <input type="submit" name="vote-store" value="Unvote" style="background:red;">
            {{ track.votes_count }}
        </form>
        <br>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes.js';
    import Form from '../../../../classes/Form.js';

    export default {
        props: ['vibe', 'track'],

        data() {
            return {
                vibes: vibes,
                downvoteTrackForm: new Form({})
            }
        },

        methods: {
            onDownvoteTrackSubmit(trackID) {
                this.vibes.downvoteTrack(this.downvoteTrackForm, this.vibe.id, trackID);
            }
        }
    }
</script>
