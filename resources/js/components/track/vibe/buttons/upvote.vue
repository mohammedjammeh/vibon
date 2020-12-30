<template>
    <div>
        <form method="POST" :action="vibes.routes.upvoteTrack(this.vibes.show.id, track.vibon_id)" @submit.prevent="onUpvoteTrackSubmit(track.vibon_id)">
            <input type="submit" name="vote-store" value="Vote">
            {{ track.votes_count }}
        </form>
        <br>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes.js';
    import Form from '../../../../classes/Form.js';

    export default {
        props: ['track'],

        data() {
            return {
                vibes: vibes,
                upvoteTrackForm: new Form({})
            }
        },

        methods: {
            onUpvoteTrackSubmit(trackID) {
                this.vibes.upvoteTrack(this.upvoteTrackForm, this.vibes.show.id, trackID);
            }
        }
    }
</script>
