<template>
    <div>
        <div v-if="this.vibes.ownedByUser(vibeID)">
            <form method="POST" :action="vibes.routes.removeTrack(vibeID, trackID)" @submit.prevent="onRemoveTrackSubmit">
                <input type="submit" name="track-vibe-destroy" :value="vibes.getVibeName(vibeID)">
            </form>
            <br>
        </div>
        <div v-else>
            <form method="POST" :action="vibes.routes.pendDetachTrack(vibeID, trackID)" @submit.prevent="onPendDetachTrackSubmit">
                <input type="submit" name="track-vibe-pend" :value="vibes.getVibeName(vibeID)">
            </form>
            <br>
        </div>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes.js';
    import Form from '../../../../classes/Form.js';

    export default {
        props: ['vibeID', 'trackID'],

        data() {
            return {
                vibes: vibes,
                removeTrackForm: new Form({}),
                pendDetachTrackForm: new Form({}),
            }
        },

        methods: {
            onRemoveTrackSubmit() {
                this.vibes.removeTrack(this.removeTrackForm, this.vibeID, this.trackID);
            },

            onPendDetachTrackSubmit() {
                this.vibes.pendDetachTrack(this.pendDetachTrackForm, this.vibeID, this.trackID);
            }
        }
    }
</script>

<style scoped>
    form input {
        background: red;
    }
</style>
