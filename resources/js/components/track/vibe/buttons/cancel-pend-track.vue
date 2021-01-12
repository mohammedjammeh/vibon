<template>
    <div>
        <form method="POST" :action="vibes.routes.cancelPendTrack(pendingTrack.id)" @submit.prevent="onCancelPendTrackSubmit">
            <input v-if="canBeRemovedByUser(pendingTrack)" type="submit" name="track-vibe-cancel-pend" :value="vibes.getVibeName(vibeID)">
            <input v-else type="submit" name="track-vibe-cancel-pend" :value="vibes.getVibeName(vibeID)" disabled>
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
                cancelPendTrackForm: new Form({}),
            }
        },

        methods: {
            canBeRemovedByUser(pendingTrack) {
                return this.vibes.ownedByUser(this.vibeID) || this.pendingTrackCore.isPendedByUser(pendingTrack);
            },

            onCancelPendTrackSubmit() {
                this.vibes.cancelPendTrack(this.cancelPendTrackForm, this.pendingTrack.id);
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
    form input{
        background: yellow;
    }
</style>
