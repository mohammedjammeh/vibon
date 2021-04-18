<template>
    <div>
        <form method="POST" :action="vibes.routes.cancelPendingAttachTrack(pendingTrack.id)" @submit.prevent="onCancelPendingAttachTrackSubmit">
            <input v-if="this.pendingTrackCore.canBeRemovedByUser(pendingTrack, vibeID)"
                type="submit" name="track-vibe-cancel-pend"
                value="Cancel Add"
            >
            <input v-else type="submit"
                name="track-vibe-cancel-pend"
                value="Cancel Add"
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
                cancelPendingAttachTrackForm: new Form({}),
            }
        },

        methods: {
            onCancelPendingAttachTrackSubmit() {
                this.vibes.cancelPendingAttachTrack(this.cancelPendingAttachTrackForm, this.pendingTrack);
            }
        },

        computed: {
            pendingTrack() {
                return this.pendingTrackCore.get(this.trackID, this.vibeID);
            },
        }
    }
</script>
