<template>
    <div>
        <form method="POST" :action="vibes.routes.removeTrack(userVibeID, trackID)" @submit.prevent="onRemoveTrackSubmit(userVibeID, trackID)">
            <input type="submit" name="track-vibe-destroy" :value="vibes.getVibeName(userVibeID)" style="background:red;">
        </form>
        <br>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes.js';
    import Form from '../../../../classes/Form.js';

    export default {
        props: ['userVibeID', 'trackID'],

        data() {
            return {
                vibes: vibes,
                removeTrackForm: new Form({}),
            }
        },

        methods: {
            onRemoveTrackSubmit(vibeID, trackVibonID) {
                this.vibes.removeTrack(this.removeTrackForm, vibeID, trackVibonID);
            }
        }
    }
</script>
