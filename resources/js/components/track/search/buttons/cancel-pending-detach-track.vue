<template>
    <div>
        <form method="POST" :action="vibes.routes.cancelPendingDetachTrack(pendingTrack.id)" @submit.prevent="onCancelPendingDetachTrackSubmit">
            <input v-if="this.pendingTrackCore.canBeRemovedByUser(pendingTrack, vibeID)"
                type="submit" name="track-vibe-cancel-pend"
                value="Cancel Remove"
            >
            <input v-else
                type="submit" name="track-vibe-cancel-pend"
                value="Cancel Remove"
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
        props: ['vibeID', 'trackID', 'searchTracks'],

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
                let track =  this.searchTracks.find(track => track.vibon_id === this.trackID);
                this.vibes.removeVibeFromTrackPendingVibesToDetach(this.vibeID, track);
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
    form input {}
</style>
