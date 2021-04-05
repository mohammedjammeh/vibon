<template>
    <div>
        <form method="POST" :action="vibes.routes.cancelPendingDetachTrack(pendingTrack.id)" @submit.prevent="onCancelPendingDetachTrackSubmit">
            <input v-if="this.pendingTrackCore.canBeRemovedByUser(pendingTrack, vibeID)"
                type="submit" name="track-vibe-cancel-pend"
                value="Cancel remove request"
            >
            <input v-else
                type="submit" name="track-vibe-cancel-pend"
                value="Cancel remove request"
                disabled
            >
        </form>
        <br>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes.js';
    import pendingTrackCore from '../../../../core/pending-track.js';
    import Form from '../../../../classes/Form.js';

    export default {
        props: ['vibeID', 'trackID'],

        data() {
            return {
                vibes: vibes,
                pendingTrackCore: pendingTrackCore,
                cancelPendingDetachTrackForm: new Form({}),
            }
        },

        methods: {
            onCancelPendingDetachTrackSubmit() {
                this.vibes.cancelPendingDetachTrack(this.cancelPendingDetachTrackForm, this.pendingTrack);
            }
        },

        computed: {
            pendingTrack() {
                return this.pendingTrackCore.get(this.trackID, this.vibeID);
            },
        }
    }
</script>

<style scoped>
    form input {
        background: orange;
    }
</style>
